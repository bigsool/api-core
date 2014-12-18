<?php


namespace Core\Module;


use Core\Action\SimpleAction;
use Core\Context\ApplicationContext;
use Core\Expression\KeyPath;
use Core\Parameter\Parameter;
use Symfony\Component\Validator\Validation;

abstract class MagicalModuleManager extends ModuleManager {

    private $modelAspects;

    /**
     * @param array $config
     */
    protected function addAspect (array $config) {

        $model = $config['model'];
        $prefix = isset($config['prefix']) ? $config['prefix'] : null;
        $constraints = isset($config['constraints']) ? $config['constraints'] : [];
        $keyPath = isset($config['keyPath']) ? new KeyPath($config['keyPath']) : null;

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
    protected function magicalCreate (array $params) {

        foreach ($this->modelAspects as $modelAspect) {
            $param = $params[$modelAspect->getPrefix()];
            Validation::createValidator()->validate($param, $modelAspect->getConstrains());
            $basicHelper = new BasicHelper();
            $appCtx = ApplicationContext::getInstance();

           // $product =

            $moduleName = $modelAspect->getModel();
            $createAction = $appCtx->getAction($moduleName,'create');
            $createAction->setParams($params);
            $createAction->process($appCtx->getAc);
          //  $models[] =
        }

        return models;

    }

    /**
     * @param string   $name
     * @param callable $processFn
     */
    protected function defineAction ($name, $params, callable $processFn) {

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