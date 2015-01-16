<?php


namespace Core\Module;


use Core\Action\Action;
use Core\Action\SimpleAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Field\KeyPath;
use Core\Filter\StringFilter;
use Core\Parameter\SafeParameter;
use Core\Registry;
use Core\Validation\RuntimeConstraintsProvider;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Validator\Validation;

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
     * @return mixed
     */
    protected function magicalModify (ActionContext $ctx, $action) {

        $appCtx = ApplicationContext::getInstance();
        $mainEntity = NULL;

        foreach ($this->modelAspects as $modelAspect) {

            // TODO HANDLE ONETOMANY
            $params = $ctx->getParams();

            $actions = $modelAspect->getActions();

            $modifyAction = array_key_exists($action, $actions) ? $actions[$action] : 'none';

            if ($modifyAction != 'none' && $actions[$action] == NULL) {
                continue;
            }

            $params =
                $modelAspect->getPrefix() ? isset($params[$modelAspect->getPrefix()])
                    ? $params[$modelAspect->getPrefix()] : NULL : [];

            if ($modifyAction == 'none') {

                $modifyAction = $this->getMagicalAction($action, $modelAspect);

            }

            $subContext = NULL;

            if ($params) {

                $subContext = new ActionContext($ctx);
                $subContext->setParams($params->getValue());

                if (!$this->isMainEntity($modelAspect) && $action == 'update') {
                    $fn = 'get' . $modelAspect->getModel();
                    $entity = $mainEntity->$fn();
                    if ($entity) {
                        $subContext->setParam('id', new SafeParameter($entity->getId()));
                    }
                }

            }


            $result = $modifyAction->process($subContext ? $subContext : $ctx);
            $this->models[$modelAspect->getModel()] = $result;
            if ($this->isMainEntity($modelAspect)) {
                $mainEntity = $result;
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

        if (!isset($mainEntity)) {
            return NULL;
        }

        $this->setRelationshipsFromMetadata();

        $entities = $this->loadEntities($mainEntity);

        return $entities;

    }

    /**
     * @param ModelAspect $modelAspect
     *
     * @return string
     */
    private function isMainEntity ($modelAspect) {

        return !$modelAspect->getKeyPath();
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

    /**
     * @return mixed
     */
    private function loadEntities ($mainEntity) {

        $appCtx = ApplicationContext::getInstance();
        $mainEntityName = $this->getMainEntityName();

        $registry = $appCtx->getNewRegistry();

        foreach ($this->models as $model) {
            $registry->save($model);
        }

        $qryCtx = new FindQueryContext($mainEntityName, new RequestContext());

        $qryCtx->addKeyPath(new \Core\Field\KeyPath('*'));

        foreach ($this->modelAspects as $modelAspect) {
            if (($keyPath = $modelAspect->getKeyPath())) {
                $qryCtx->addKeyPath($keyPath);
            }
        }

        $qryCtx->addFilter(new StringFilter($mainEntityName, 'bla', 'id = ' . $mainEntity->getId()));

        $result = $registry->find($qryCtx, false);

        return $result[0];

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


        $constraints = [];
        if (isset($config['constraints'])) {
            $constraints = $config['constraints'];
            if (!is_array($config['constraints'])) {
                throw new \RuntimeException('invalid constraints');
            }
            foreach ($constraints as $constraint) {
                if (!is_a($constraint, 'Symfony\Component\Validator\Constraint')
                    && !is_a($constraint, 'Core\Validation\Constraints\Dictionary')
                ) {
                    throw new \RuntimeException('invalid constraints');
                }
            }
        }

        $actions = [];
        if (isset($config['actions'])) {
            if (!is_array($config['actions'])) {
                throw new \RuntimeException('invalid constraints');
            }
            foreach ($config['actions'] as $action) {
                if ($action && !is_a($action, 'Core\Action\Action')) {
                    throw new \RuntimeException('invalid action');
                }
            }
            $actions = $config['actions'];
        }

        $keyPath = isset($config['keyPath']) ? new KeyPath($config['keyPath']) : NULL;

        if (!$keyPath) {
            foreach ($this->modelAspects as $modelAspect) {
                if (!$modelAspect->getKeyPath()) {
                    throw new \RuntimeException('two main entities');
                }
            }
        }

        $this->modelAspects[] = new ModelAspect($model, $prefix, $constraints, $actions, $keyPath);

    }

    /**
     * @return ModelAspect[]
     */
    protected function getAspects () {

        return $this->modelAspects;

    }

    /**
     * @param $modelName
     *
     * @return ModelAspect
     */
    protected function getModelAspectForModelName($modelName) {

        foreach ($this->getAspects() as $modelAspect) {
            if ($modelAspect->getModel() == $modelName) {
                return $modelAspect;
            }
        }

        throw new \RuntimeException('ModelAspect not found');

    }

    protected function magicalFind ($keyPaths, $filters) {

        $appCtx = ApplicationContext::getInstance();

        $registry = $appCtx->getNewRegistry();

        $mainEntityName = $this->getMainEntityName();

        $qryCtx = new FindQueryContext($mainEntityName, new RequestContext());

        foreach ($keyPaths as $keyPath) {
            $qryCtx->addKeyPath($keyPath);
        }

        foreach ($filters as $filter) {
            $qryCtx->addFilter($filter);
        }

        $result = $registry->find($qryCtx, false);

        return $result;

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
            function ($actionContext) use ($processFn, $modelAspects) {

                $params = $actionContext->getParams();
                foreach ($modelAspects as $modelAspect) {
                    if (!$modelAspect->getPrefix()) {
                        continue;
                    }
                    $param = $params[$modelAspect->getPrefix()];
                    $constraintViolationList =
                        Validation::createValidator()->validate($param->getValue(), $modelAspect->getConstraints());
                    if ($constraintViolationList->count()) {
                        throw new \RuntimeException('constraint violation');
                    }
                }

                return $processFn($actionContext);
            }));

    }

    protected function getModuleName () {

        $className = get_called_class();
        $classNameExploded = explode('\\', $className);

        return $classNameExploded[count($classNameExploded) - 2];

    }

    /**
     * @param string $action
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

}