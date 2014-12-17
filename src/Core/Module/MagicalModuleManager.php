<?php


namespace Core\Module;


use Core\Action\SimpleAction;
use Core\Context\ApplicationContext;
use Core\Parameter\Parameter;
use Symfony\Component\Validator\Validation;

abstract class MagicalModuleManager extends ModuleManager {

    private $modelAspects;

    /**
     * @param array $config
     */
    protected function addAspect (array $config) {
        $this->modelAspects[] = new ModelAspect($config['model'],
                                                $config['prefix'],
                                                $config['constraints'],
                                                $config['keyPath']);
    }

    /**
     * @return ModelAspect[]
     */
    protected function getAspects() {

    }

    /**
     * @param Parameter[] $params
     */
    protected function magicalCreate (array $params) {

        // foreach modelAspects
             // validate()
            // entitiesCreated[] = call BasicHelper create with param
        foreach ($this->modelAspects as $modelAspect) {
            $param = $params[$modelAspect->getPrefix()];
            Validation::createValidator()->validate($param, $modelAspect->getConstrains());
            $basicHelper = new BasicHelper();
            $models[] = $basicHelper->create($modelAspect->getModel(),$params);
        }

        return models;

    }

    /**
     * @param string   $name
     * @param callable $processFn
     */
    protected function defineAction ($name, $params, callable $processFn) {
        $className = get_called_class();
        $classNameExploded = explode('\\',$className);
        $module = $classNameExploded[count($classNameExploded) - 2];
        $appCtx = ApplicationContext::getInstance();
        $appCtx->addAction(new SimpleAction($module,$name,[], $params,$processFn));
    }

} 