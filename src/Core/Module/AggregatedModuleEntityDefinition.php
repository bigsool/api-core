<?php


namespace Core\Module;


use Core\Context\ApplicationContext;
use Core\Registry;
use Core\Validation\Parameter\Constraint;

abstract class AggregatedModuleEntityDefinition extends ModuleEntityDefinition {

    /**
     * @var string
     */
    protected $dbEntityName;

    /**
     * @var ModelAspect
     */
    protected $mainAspect;

    /**
     * @var ModelAspect[]
     */
    protected $modelAspects = [];

    /**
     * @return ModelAspect
     */
    public function getMainAspect () {

        return $this->mainAspect;

    }

    /**
     * @param array $params
     *
     * @return Constraint[][]
     */
    public function getConstraints (array &$params) {

        $constraints = parent::getConstraints($isCreation, $params);

        foreach ($this->getModelAspects() as $modelAspect) {

            $modelAspectConstraints = [];

            foreach ($modelAspect->getConstraints() as $actionConstraints) {
                $modelAspectConstraints = array_merge($modelAspectConstraints, $actionConstraints);
            }

            $constraints[$modelAspect->getPrefix()] = $modelAspectConstraints;

        }

    }

    /**
     * @return string
     */
    public function getEntityName () {

        return $this->dbEntityName;

    }

    /**
     * Returns
     * @return ModelAspect[]
     */
    public function getModelAspects () {

        return $this->modelAspects;

    }


    /**
     * @return ModelAspect[]
     */
    public function getAllModelAspects () {

        return array_merge([$this->getMainAspect()], $this->getModelAspects());

    }

    /**
     * @param array $config
     */
    protected function setMainEntityAspect ($config) {

        $this->mainAspect = $this->getModelAspectFromConfig($config);
        $this->dbEntityName = $config['model'];

    }

    /**
     * @param array $config
     *
     * @return ModelAspect
     */
    protected function getModelAspectFromConfig (array $config) {

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

        $module = $model;
        if (isset($config['module'])) {
            $module = $config['module'];
            if (!is_string($module)) {
                throw new \RuntimeException('invalid module');
            }
        }

        $relativeField = isset($config['keyPath']) ? $config['keyPath'] : NULL;
        if (!$relativeField) {
            foreach ($this->modelAspects as $modelAspect) {
                if (!$modelAspect->getRelativeField()) {
                    throw new \RuntimeException('two main entities');
                }
            }
        }

        $withPrefixedFields = isset($config['withPrefixedFields']) ? $config['withPrefixedFields'] : false;

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

        $modelAspect =
            new ModelAspect($model, $module, $prefix, $constraints, $actions, $relativeField, $withPrefixedFields);
        ApplicationContext::getInstance()->finalizeModelAspect($modelAspect);

        return $modelAspect;
    }

    /**
     * @param array $config
     */
    protected function addAspect (array $config) {

        $modelAspect = $this->getModelAspectFromConfig($config);
        $this->modelAspects[$modelAspect->getRelativeField()] = $modelAspect;

    }

}