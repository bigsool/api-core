<?php


namespace Core\Module;


use Core\Action\SimpleAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Field\KeyPath;
use Core\Filter\StringFilter;
use Core\Parameter\Parameter;
use Core\Registry;
use Core\Validation\RuntimeConstraintsProvider;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Validator\Validation;

abstract class MagicalModuleManager extends ModuleManager {

    /**
     * @var array
     */
    private $modelAspects = [];


    /**
     * @var array
     */
    private $models = [];


    /**
     * @param array $config
     */
    protected function addAspect (array $config) {

        $prefix = null;
        if (isset($config['prefix'])) {
            $prefix = $config['prefix'];
            if (!is_string($prefix)) throw new \RuntimeException('invalid model');
        }

        $model = null;
        if (isset($config['model'])) {
            $model = $config['model'];
            Registry::realModelClassName($model);
            if (!is_string($model)) throw new \RuntimeException('invalid model');
        }


        $constraints = [];
        if (isset($config['constraints'])) {
            $constraints = $config['constraints'];
            if (!is_array($config['constraints'])) throw new \RuntimeException('invalid constraints');
            foreach ($constraints as $constraint) {
                if (!is_a($constraint, 'Symfony\Component\Validator\Constraint') && !is_a($constraint , 'Core\Validation\Constraints\Dictionary')) {
                    throw new \RuntimeException('invalid constraints');
                }
            }
        }

        $actions = [];
        if (isset($config['actions'])) {
            if (!is_array($config['actions'])) throw new \RuntimeException('invalid constraints');
            foreach ($config['actions'] as $action) {
                if ($action && !is_a($action,'Core\Action\Action')) {
                    throw new \RuntimeException('invalid action');
                }
            }
            $actions = $config['actions'];
        }

        $keyPath = isset($config['keyPath']) ? new KeyPath($config['keyPath']) : null;

        if (!$keyPath) {
            foreach ($this->modelAspects as $modelAspect) {
                if (!$modelAspect->getKeyPath()) {
                    throw new \RuntimeException('two main entities');
                }
            }
        }

        $this->modelAspects[] = new ModelAspect($model,$prefix,$constraints, $actions, $keyPath);

    }

    /**
     * @return ModelAspect[]
     */
    protected function getAspects() {

        return $this->modelAspects;

    }

    public function magicalCreate (ActionContext $ctx) {
        return $this->magicalModify($ctx,'create');
    }


    public function magicalUpdate (ActionContext $ctx) {
        return $this->magicalModify($ctx,'create');
    }

    /**
     * @param Parameter[] $params
     * @return Model[] models
     */
    protected function magicalModify (ActionContext $ctx, $action) {

        $appCtx = ApplicationContext::getInstance();

        foreach ($this->modelAspects as $modelAspect) {

            $params = $ctx->getParams();

            $actions = $modelAspect->getActions();
            $modifyAction = array_key_exists($action,$actions) ? $actions[$action] : 'none';

            if ($modifyAction != 'none' && $actions[$action] == NULL) continue;

            $params = $modelAspect->getPrefix() ? isset($params[$modelAspect->getPrefix()]) ? $params[$modelAspect->getPrefix()] : null : [];

            if ($modifyAction == 'none') {

                $product = $appCtx->getProduct();
                try {
                    $modifyAction = $appCtx->getAction($product . '\\' . $modelAspect->getModel(), $action,[]);
                }
                catch (\RuntimeException $e) {
                    $modifyAction = $appCtx->getAction('Core\\'.$modelAspect->getModel(),$action,[]);
                }

            }

            $subContext = null;
            if ($params) {
                $subContext = new ActionContext($ctx);
                $subContext->clearParams();
                $subContext->setParams($params->getValue());
            }

            $result = $modifyAction->process($subContext ? $subContext : $ctx);
            $this->models[$modelAspect->getModel()] = $result;
            if ($this->isMainEntity($modelAspect)) {
                $mainEntity = $result;
            }

        }

        if (!isset($mainEntity)) return null;

        $mainEntityName = $this->getMainEntityName();

        $metadata = $appCtx->getClassMetadata('Core\Model\\'.$mainEntityName);
        $mapping = $metadata->getAssociationMappings();

        foreach ($mapping as $elem) {

            $source =  $mainEntityName;

            $target =  explode('\\',$elem['targetEntity']);
            $target = $target[count($target) - 1];

            if (!array_key_exists($target,$this->models)) continue;

            $field1 = $elem['fieldName'];
            $field2 = $elem['mappedBy'];

            $prefix1 = 'set';
            $prefix2 = 'set';

            if ($elem['type'] == ClassMetadataInfo::ONE_TO_MANY || $elem['type'] == ClassMetadataInfo::MANY_TO_MANY) {
                $field1 = substr($field1,0,strlen($field1) - 1);
                $prefix1 = "add";
            }

            if ($elem['type'] == ClassMetadataInfo::MANY_TO_ONE || $elem['type'] == ClassMetadataInfo::MANY_TO_MANY) {
                $field2 = $elem['inversedBy'];
                $field2 = substr($field2,0,strlen($field2) - 1);
                $prefix2 = "add";
            }

            $fn = $prefix1.ucfirst($field1);
            $this->models[$source]->$fn($this->models[$target]);

            $fn = $prefix2.ucfirst($field2);
            $this->models[$target]->$fn($this->models[$source]);

        }

        $registry = $appCtx->getNewRegistry();

        foreach ($this->models as $model) {
            $registry->save($model);
        }

        $qryCtx = new FindQueryContext($mainEntityName, new RequestContext());

        $qryCtx->addKeyPath(new \Core\Field\KeyPath('*'));

        foreach($this->modelAspects as $modelAspect) {
            if (($keyPath = $modelAspect->getKeyPath())) {
                $qryCtx->addKeyPath($keyPath);
            }
        }

        $qryCtx->addFilter(new StringFilter($mainEntityName,'bla','id = '.$mainEntity->getId()));

        $result = $registry->find($qryCtx,false);

        return $result[0];

    }

    protected function magicalFind ($ids) {

        $appCtx = ApplicationContext::getInstance();

        $registry = $appCtx->getNewRegistry();

        $mainEntityName = $this->getMainEntityName();

        $qryCtx = new FindQueryContext($mainEntityName, new RequestContext());

        $qryCtx->addKeyPath(new \Core\Field\KeyPath('*'));

        foreach($this->modelAspects as $modelAspect) {
            if (($keyPath = $modelAspect->getKeyPath())) {
                $qryCtx->addKeyPath($keyPath);
            }
        }

        $inClause = '('.$ids[0].',';
        for ($i = 1 ; $i < count($ids) ; ++$i) {
            $inClause .= ','.$ids[$i];
        }
        $inClause .= ')';

        $qryCtx->addFilter(new StringFilter($mainEntityName,'bla','id IN '.$inClause));

        $result = $registry->find($qryCtx,false);

        return $result;

    }

    private function getMainEntityName () {
        foreach ($this->modelAspects as $modelAspect) {
            if ($modelAspect->getKeyPath() == '') {
                return $modelAspect->getModel();
            }
        }
        return null;
    }

    private function isMainEntity ($modelAspect) {
        return !$modelAspect->getKeyPath();
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

        $appCtx->addAction(new SimpleAction($module,$name,[], $params,function($actionContext) use ($processFn, $modelAspects) {
            $params = $actionContext->getParams();
            foreach ($modelAspects as $modelAspect) {
                if (!$modelAspect->getPrefix()) continue;
                $param = $params[$modelAspect->getPrefix()];
                $constraintViolationList = Validation::createValidator()->validate($param->getValue(), $modelAspect->getConstraints());
                if ($constraintViolationList->count()) {
                    throw new \RuntimeException('constraint violation');
                }
            }
            return $processFn($actionContext);
        }));

    }

    protected function getModuleName () {

        $className = get_called_class();
        $classNameExploded = explode('\\',$className);

        return $classNameExploded[count($classNameExploded) - 2];

    }

}