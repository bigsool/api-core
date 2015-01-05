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
use Symfony\Component\Validator\Validation;

abstract class MagicalModuleManager extends ModuleManager {

    private $modelAspects = [];
    private $models = [];
    private $relationships = [];

    public function addRelationship (array $relationship) {
        $this->relationships[] = $relationship;
    }

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


    /**
     * @param Parameter[] $params
     * @return Model[] models
     */
    protected function magicalCreate (ActionContext $ctx) {

        $params = $ctx->getParams();

        $appCtx = ApplicationContext::getInstance();

        foreach ($this->modelAspects as $modelAspect) {

            $actions = $modelAspect->getActions();
            $createAction = array_key_exists('create',$actions) ? $actions['create'] : 'none';

            if ($createAction != 'none' && $actions['create'] == NULL) continue;

            $ctxCopy = $ctx;

            $param = $modelAspect->getPrefix() ? isset($params[$modelAspect->getPrefix()]) ? $params[$modelAspect->getPrefix()] : null : [];
            Validation::createValidator()->validate($param, $modelAspect->getConstraints());

            if ($createAction == 'none') {

                $product = $appCtx->getProduct();
                try {
                    $createAction = $appCtx->getAction($product . '\\' . $modelAspect->getModel(), 'create',[]);
                }
                catch (\RuntimeException $e) {
                    $createAction = $appCtx->getAction('Core\\'.$modelAspect->getModel(),'create',[]);
                }

            }

            if ($param) {
                $ctxCopy->setParams($param);
            }

            $result = $createAction->process($ctxCopy);
            $this->models[strtolower($modelAspect->getModel())] = $result;
            if ($this->isMainEntity($modelAspect)) {
                $mainEntity = $result;
            }

        }

        if (!isset($mainEntity)) return null;

        foreach ($this->relationships as $relationship) {
            $fn = 'set'.ucfirst($relationship['attribute']);
            $this->models[$relationship['model1']]->$fn($this->models[$relationship['model2']]);
        }


        $registry = $appCtx->getNewRegistry();


        foreach ($this->models as $model) {
            $registry->save($model);
        }

        $mainEntityName = $this->getMainEntityName();

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

        $appCtx->addAction(new SimpleAction($module,$name,[], $params,$processFn));

    }

    protected function getModuleName () {

        $className = get_called_class();
        $classNameExploded = explode('\\',$className);

        return $classNameExploded[count($classNameExploded) - 2];

    }

}