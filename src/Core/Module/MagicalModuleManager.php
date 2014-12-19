<?php


namespace Core\Module;


use Core\Action\SimpleAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Expression\KeyPath;
use Core\Filter\StringFilter;
use Core\Parameter\Parameter;
use Core\Validation\RuntimeConstraintsProvider;
use Symfony\Component\Validator\Validation;

abstract class MagicalModuleManager extends ModuleManager {

    private $modelAspects = [];

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
            if (!is_string($model)) throw new \RuntimeException('invalid model');
        }

        $constraints = [];
        if (isset($config['constraints'])) {
            $constraints = $config['constraints'];
            if (!is_array($config['constraints'])) throw new \RuntimeException('invalid constraints');
            foreach ($constraints as $constraint) {
                $fgfgd = get_class($constraint);
                if (!is_a($constraint, 'Symfony\Component\Validator\Constraint') && !is_a($constraint , 'Core\Validation\Constraints\Dictionary')) {
                    throw new \RuntimeException('invalid constraints');
                }
            }
        }

        $keyPath = isset($config['keyPath']) ? new KeyPath($config['keyPath']) : null;

        if (!$keyPath) {
            foreach ($this->modelAspects as $modelAspect) {
                if (!$modelAspect->getKeyPath()) {
                    throw new \RuntimeException('two main entities');
                }
            }
        }




        $this->modelAspects[] = new ModelAspect($model,$prefix,$constraints,$keyPath);

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

        foreach ($this->modelAspects as $modelAspect) {

            $param = $modelAspect->getPrefix() ? $params[$modelAspect->getPrefix()] : [];
            Validation::createValidator()->validate($param, $modelAspect->getConstraints());

            $appCtx = ApplicationContext::getInstance();

            $product = $appCtx->getProduct();

            try {
                $createAction = $appCtx->getAction($product . '\\' . $modelAspect->getModel(), 'create',[],$params);
            }
            catch (\Exception $e) {
                $createAction = $appCtx->getAction('Core\\'.$modelAspect->getModel(),'create',[],$params);
            }

            $result = $createAction->process($ctx);
            if ($this->isMainEntity(modelAspect)) {
                $mainEntity = $result;
            }

        }

        $mainEntityName = $this->getMainEntityName();

        $qryCtx = new FindQueryContext('User', $appCtx->getRequestContext());

        $qryCtx->addKeyPath(new KeyPath('*'));

        foreach($this->modelAspects as $modelAspect) {

            if (($keyPath = $modelAspect->getKeyPath())) {
                $qryCtx->addKeyPath($keyPath);
            }

        }

        $findQueryContext = new FindQueryContext($mainEntityName,$qryCtx);
        $findQueryContext->addFilter(new StringFilter($mainEntityName,'bla','id = '.$mainEntity->getId()));

        $registry = $appCtx->getNewRegistry();

        $result = $registry->find($findQueryContext);

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
        return !$modelAspect->getPath();
    }

    /**
     * @param string   $name
     * @param callable $processFn
     */
    protected function defineAction ($name, $params, callable $processFn) {

        foreach ($params as $key => $value) {
            $params[$key][1] = new RuntimeConstraintsProvider($value[1]);
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