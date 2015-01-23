<?php


namespace Core\Module;


use Core\Action\Action;
use Core\Action\SimpleAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Expression\AbstractKeyPath;
use Core\Field\KeyPath;
use Core\Parameter\UnsafeParameter;
use Core\Registry;
use Core\Validation\RuntimeConstraintsProvider;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Yaml\Exception\RuntimeException;

abstract class MagicalModuleManager extends ModuleManager {

    /**
     * @var ModelAspect[]
     */
    private $modelAspects = [];

    /**
     * @var array
     */
    private $models = [];

    /**
     * @var string
     */
    private $mainEntityName = NULL;

    /**
     * @var mixed
     */
    private $mainEntity = NULL;

    /**
     * @param ActionContext $ctx
     *
     * @return mixed
     */
    public function magicalCreate (ActionContext $ctx) {

        return $this->magicalModify($ctx, 'create');
    }

    /**
     * @param ActionContext $ctx
     * @param string        $action
     *
     * @return MagicalEntity
     */
    protected function magicalModify (ActionContext $ctx, $action) {

        if (!$this->mainEntityName) {
            throw new RuntimeException('main entity undefined !');
        }

        $prefixes = [];
        foreach ($this->modelAspects as $modelAspect) {
            $prefixes[] = $modelAspect->getPrefix();
        }
        foreach ($this->modelAspects as $modelAspect) {

            // TODO HANDLE ONETOMANY
            $params = $ctx->getParams();

            $actions = $modelAspect->getActions();

            $modifyAction = array_key_exists($action, $actions) ? $actions[$action] : 'none';


            if ($modifyAction != 'none' && $actions[$action] == NULL) {
                continue;
            }

            $params = $modelAspect->getPrefix()
                ? (isset($params[$modelAspect->getPrefix()])
                    ? $params[$modelAspect->getPrefix()]
                    : NULL)
                : [];

            if ($modifyAction == 'none') {

                $modifyAction = $this->getMagicalAction($action, $modelAspect);

            }

            $subContext = NULL;

            if ($params) {

                $subContext = new ActionContext($ctx);
                $subContext->setParams(UnsafeParameter::getFinalValue($params));
                if (!$this->isMainEntity($modelAspect) && $action == 'update') {
                    $entity = $this->getEntityFromKeyPath($modelAspect->getKeyPath());
                    if ($entity) {
                        $subContext->setParam('id', $entity->getId());
                    }
                }

            }
            else {
                // Create a new context and remove parameters which are used by model aspects
                $subContext = new ActionContext($ctx);
                $subContext->clearParams();
                foreach ($ctx->getParams() as $key => $value) {
                    if (!in_array($key, $prefixes, true)) {
                        $subContext->setParam($key, $value);
                    }
                }
            }

            $result = $modifyAction->process($subContext ? $subContext : $ctx);

            $this->models[$modelAspect->getModel()] = $result;

            if ($this->isMainEntity($modelAspect)) {
                $this->mainEntity = $result;
            }

            if ($subContext) {
                $iterator = $subContext->getIterator();
                while ($iterator->valid()) {
                    $key = $iterator->key();
                    if (!isset($ctx[$key])) {
                        $ctx[$key] = $iterator->current();
                    }
                    $iterator->next();
                }
            }

        }

        if (!isset($this->mainEntity)) {
            return NULL;
        }

        $this->setRelationshipsFromMetadata();

        $this->saveEntities();

        return $this->getMagicalEntityObject($this->mainEntity);

    }


    /**
     * @param string      $action
     * @param ModelAspect $modelAspect
     *
     * @return Action
     */
    protected function getMagicalAction ($action, ModelAspect $modelAspect) {

        $appCtx = ApplicationContext::getInstance();

        $product = $appCtx->getProduct();
        try {
            $modifyAction = $appCtx->getAction($product . '\\' . $modelAspect->getModel(), $action, []);
        }
        catch (\RuntimeException $e) {
            $modifyAction = $appCtx->getAction('Core\\' . $modelAspect->getModel(), $action, []);
        }

        return $modifyAction;
    }

    /**
     * @param ModelAspect $modelAspect
     *
     * @return string
     */
    private function isMainEntity ($modelAspect) {

        return !$modelAspect->getKeyPath();
    }

    /**
     * @param AbstractKeyPath $keyPath
     *
     * @return mixed
     */
    private function getEntityFromKeyPath (AbstractKeyPath $keyPath) {

        $models = explode('.', $keyPath->getValue());
        $entity = $this->mainEntity;
        foreach ($models as $model) {
            $fn = 'get' . ucfirst($model);
            $entity = $entity->$fn();
        }

        return $entity;
    }

    private function setRelationshipsFromMetadata () {

        $modelNameForKeyPath = [];

        foreach ($this->modelAspects as $modelAspect) {

            if ($this->isMainEntity($modelAspect)) {
                continue;
            }

            $keyPath = $modelAspect->getKeyPath()->getValue();
            $modelNameForKeyPath[$keyPath] = $modelAspect->getModel();

            // Si prefix ne contient pas de . alors on se base de mainEntity
            $pos = strrpos($keyPath, '.');
            if ($pos === false) {
                $modelName = $this->getMainEntityName();
                $lastKeyPath = $keyPath;
            }
            else {
                if (!isset($modelNameForKeyPath[substr($keyPath, 0, $pos)])) {
                    throw new \RuntimeException('model name not defined for this prefix');
                }
                $modelName = $modelNameForKeyPath[substr($keyPath, 0, $pos)];
                $lastKeyPath = substr($keyPath, $pos + 1);
            }

            $mainEntityClassName = Registry::realModelClassName($modelName);
            $metadata = ApplicationContext::getInstance()->getClassMetadata($mainEntityClassName);
            $mapping = $metadata->getAssociationMapping($lastKeyPath);

            $this->setRelationshipsFromAssociationMapping($modelName, $mapping);

        }

    }

    private function getMainEntityName () {

        foreach ($this->modelAspects as $modelAspect) {
            if ($modelAspect->getKeyPath() == '') {
                return $modelAspect->getModel();
            }
        }

        return NULL;
    }

    /**
     * @param string $sourceModelName
     * @param        $mapping
     */
    private function setRelationshipsFromAssociationMapping ($sourceModelName, array $mapping) {

        $target = explode('\\', $mapping['targetEntity']);
        $target = $target[count($target) - 1];

        /*if (!array_key_exists($target, $this->models)) {
            continue;
        }*/

        $field1 = $mapping['fieldName'];
        $field2 = $mapping['mappedBy'];

        $prefix1 = 'set';
        $prefix2 = 'set';

        if ($mapping['type'] == ClassMetadataInfo::ONE_TO_MANY || $mapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {
            $field1 = substr($field1, 0, strlen($field1) - 1);
            $prefix1 = "add";
        }

        if ($mapping['type'] == ClassMetadataInfo::MANY_TO_ONE || $mapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {
            $field2 = $mapping['inversedBy'];
            $field2 = substr($field2, 0, strlen($field2) - 1);
            $prefix2 = "add";
        }

        $fn = $prefix1 . ucfirst($field1);
        $this->models[$sourceModelName]->$fn($this->models[$target]);

        $fn = $prefix2 . ucfirst($field2);
        $this->models[$target]->$fn($this->models[$sourceModelName]);
    }

    private function saveEntities () {

        $appCtx = ApplicationContext::getInstance();

        $registry = $appCtx->getNewRegistry();

        foreach ($this->models as $model) {
            $registry->save($model);
        }

    }

    /**
     * @return MagicalEntity
     */
    public function getMagicalEntityObject ($entity) {

        $className = Registry::realModelClassName($this->getModuleName());

        return new $className($entity);

    }

    protected function getModuleName () {

        $className = get_called_class();
        $classNameExploded = explode('\\', $className);

        return $classNameExploded[count($classNameExploded) - 2];

    }

    public function formatResult () {

        $entities[lcfirst($this->getMainEntityName())] = $this->mainEntity;
        foreach ($this->modelAspects as $modelAspect) {
            if (($keyPath = $modelAspect->getKeyPath())) {
                $entities[lcfirst($modelAspect->getModel())] = $this->getEntityFromKeyPath($keyPath);
            }
        }

        return $entities;

    }

    /**
     * @param ActionContext $ctx
     *
     * @return mixed
     */
    public function magicalUpdate (ActionContext $ctx) {

        return $this->magicalModify($ctx, 'update');
    }

    /**
     * @param ApplicationContext $context
     */
    public function load (ApplicationContext &$context) {

        $this->loadAspects();

        parent::load($context);

    }

    /**
     * @return mixed
     */
    public abstract function loadAspects ();

    /**
     * @return mixed
     */
    protected function getMainEntity () {

        return $this->mainEntity;

    }

    /**
     * @param array $config
     */
    protected function setMainEntity ($config) {

        $this->addAspect($config);
        $this->mainEntityName = $config['model'];

    }

    /**
     * @param array $config
     */
    protected function addAspect (array $config) {

        $prefix = NULL;
        if (isset($config['prefix'])) {
            $prefix = $config['prefix'];
            if (!is_string($prefix)) {
                throw new \RuntimeException('invalid model');
            }
        }

        $model = NULL;
        if (isset($config['model'])) {
            $model = $config['model'];
            Registry::realModelClassName($model);
            if (!is_string($model)) {
                throw new \RuntimeException('invalid model');
            }
        }

        $keyPath = isset($config['keyPath']) ? new KeyPath($config['keyPath']) : NULL;
        if (!$keyPath) {
            foreach ($this->modelAspects as $modelAspect) {
                if (!$modelAspect->getKeyPath()) {
                    throw new \RuntimeException('two main entities');
                }
            }
        }

        $actionNames = ['create', 'find', 'update', 'delete'];
        $constraints = [];
        $actions = [];
        foreach ($actionNames as $actionName) {
            if (!isset($config[$actionName])) {
                continue;
            }
            $configOfTheAction = $config[$actionName];

            if (isset($configOfTheAction['constraints'])) {
                $constraints[$actionName] = $configOfTheAction['constraints'];
                if (!is_array($configOfTheAction['constraints'])) {
                    throw new \RuntimeException('invalid constraints');
                }
                foreach ($constraints[$actionName] as $constraint) {
                    if (!($constraint instanceof Constraint)) {
                        throw new \RuntimeException('invalid constraint');
                    }
                }
            }

            if (isset($configOfTheAction['action'])) {
                $actions[$actionName] = $configOfTheAction['action'];
                if ($configOfTheAction['action'] && !is_a($configOfTheAction['action'], 'Core\Action\Action')) {
                    throw new \RuntimeException('invalid action');
                }
            }
        }

        $this->modelAspects[] = new ModelAspect($model, $prefix, $constraints, $actions, $keyPath);

    }

    /**
     * @param $modelName
     *
     * @return ModelAspect
     */
    protected function getModelAspectForModelName ($modelName) {

        foreach ($this->getAspects() as $modelAspect) {
            if ($modelAspect->getModel() == $modelName) {
                return $modelAspect;
            }
        }

        throw new \RuntimeException('ModelAspect not found');

    }

    /**
     * @return ModelAspect[]
     */
    public function getAspects () {

        return $this->modelAspects;

    }

    /**
     * @param String[] $values
     * @param String[] $alias
     * @param Filter[] $filters
     * @param Boolean $hydrateArray
     * @return mixed
     */
    protected function magicalFind ($values, $alias, $filters, $hydrateArray = false) {

        $appCtx = ApplicationContext::getInstance();

        $registry = $appCtx->getNewRegistry();

        $qryCtx = new FindQueryContext($this->mainEntityName, new RequestContext());

        foreach ($values as $value) {
            $valueArray = explode('.', $value);
            $model = $valueArray[0];
            $newValue = $valueArray[1];
            foreach ($this->modelAspects as $modelAspect) {
                if (!$modelAspect->getKeyPath()) {
                    continue;
                }
                $modeAspectKeyPath = explode('.', $modelAspect->getKeyPath()->getValue());
                if ($modeAspectKeyPath[count($modeAspectKeyPath) - 1] == $model) {
                    $newValue = $modelAspect->getKeyPath()->getValue();
                    unset($valueArray[0]);
                    foreach ($valueArray as $keyPath) {
                        $newValue .= '.' . $keyPath;
                    }
                }
            }
            $valueAlias = isset($alias[$value]) ? $alias[$value] : NULL;
            $qryCtx->addKeyPath(new KeyPath($newValue), $valueAlias);
        }

        foreach ($filters as $filter) {
            $qryCtx->addFilter($filter);
        }

        $result = $registry->find($qryCtx, $hydrateArray);

        return $hydrateArray ? $result : $this->formatFindResult($result);

    }

    /**
     * @param Filter[] $filters
     * @return mixed
     */
    public function magicalDelete ($filters) {

        $appCtx = ApplicationContext::getInstance();

        $registry = $appCtx->getNewRegistry();

        $qryCtx = new FindQueryContext('User', new RequestContext());

        $qryCtx->addKeyPath(new KeyPath('*'));
        foreach ($this->modelAspects as $modelAspect) {
            if ($modelAspect->getKeyPath()) $qryCtx->addKeyPath($modelAspect->getKeyPath());
        }

        foreach ($filters as $filter) $qryCtx->addFilter($filter);

        $result = $registry->find($qryCtx, false);

        $registry->delete($result[0]);

    }

    /**
     * @param string   $name
     * @param array    $params
     * @param callable $processFn
     */
    protected function defineAction ($name, Array $params, callable $processFn) {

        foreach ($params as $key => $value) {
            $params[$key][1] = new RuntimeConstraintsProvider([$key => $value[1]]);
        }

        $module = $this->getModuleName();
        $appCtx = ApplicationContext::getInstance();
        $modelAspects = $this->modelAspects;

        $appCtx->addAction(new SimpleAction($module, $name, [], $params,
            function (ActionContext $actionContext) use ($processFn, &$modelAspects, &$name) {

                $params = $actionContext->getParams();
                $prefixes = [];
                foreach ($modelAspects as $modelAspect) {
                    if (!$modelAspect->getPrefix()) {
                        continue;
                    }
                    $prefixes[] = $modelAspect->getPrefix();
                    $param = $params[$modelAspect->getPrefix()];
                    $constraintViolationList =
                        Validation::createValidator()->validate(UnsafeParameter::getFinalValue($param),
                                                                $modelAspect->getConstraints($name));
                    if ($constraintViolationList->count()) {
                        throw new \RuntimeException('constraint violation');
                    }
                }

                return $processFn($actionContext);

            }));

    }

    /**
     * @param Array   $result
     * @return Array
     */
    protected function formatFindResult ($result) {
        $entities = [];
        foreach ($result as $elem) {
            if (is_array($elem)) {
                $entities[] = $this->getMagicalEntityObject($elem[0]);
            }
            else {
                $entities[] = $this->getMagicalEntityObject($elem);
            }
        }
        return $entities;
    }



}