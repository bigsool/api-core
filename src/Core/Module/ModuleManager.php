<?php


namespace Core\Module;


use Core\Action\Action;
use Core\Context\ApplicationContext;
use Core\Filter\Filter;
use Core\Rule\Rule;

abstract class ModuleManager {

    /**
     * @var ModuleEntity[]
     */
    protected $moduleEntities = [];

    /**
     * @param ApplicationContext $context
     */
    public function load (ApplicationContext &$context) {

        $context->addModuleManager($this);

        $moduleEntityDefinitions = $this->createModuleEntityDefinitions($context);

        foreach ($moduleEntityDefinitions as $moduleEntityDefinition) {
            $moduleEntity = $moduleEntityDefinition instanceof AggregatedModuleEntityDefinition ?
                new AggregatedModuleEntity($context, $moduleEntityDefinition)
                : new DbModuleEntity($context, $moduleEntityDefinition);
            $this->moduleEntities[$moduleEntity->getDefinition()->getEntityName()] = $moduleEntity;
            $context->addModuleEntity($moduleEntity);
        }

        // loading of model aspect must be done after the definition of all Module Entities
        // so loadModuleEntities is called later by Application

        foreach ($this->moduleEntities as $moduleEntity) {

            foreach ($moduleEntity->getDefinition()->getFilters() as $filter) {
                $context->addFilter($filter);
            }

            $entityName = $moduleEntity->getDefinition()->getDBEntityName();

            foreach ($moduleEntity->getDefinition() as $fieldName => $calculatedField) {
                $context->addCalculatedField($entityName, $fieldName, $calculatedField);
            }

        }

        foreach ($this->createModuleFilters($context) as $filter) {
            $context->addFilter($filter);
        }

        foreach ($this->createRules($context) as $rule) {
            $context->addRule($rule);
        }

        foreach ($this->createActions($context) as $action) {
            $context->addAction($action);
        }

        $namespace = (new \ReflectionClass($this))->getNamespaceName();

        if (class_exists($classname = $namespace . '\\API')) {
            /**
             * @var API $API
             */
            $API = new $classname();
            $API->load();
        }

    }

    /**
     * @param ApplicationContext $context
     *
     * @return ModuleEntityDefinition[]
     */
    public function createModuleEntityDefinitions (ApplicationContext &$context) {

        return [];

    }

    /**
     * @param ApplicationContext $context
     *
     * @return Filter[]
     */
    public function createModuleFilters (ApplicationContext &$context) {

        return [];

    }

    /**
     * @param ApplicationContext $context
     *
     * @return Rule[]
     */
    public function createRules (ApplicationContext &$context) {

        return [];

    }

    /**
     * @param ApplicationContext $context
     *
     * @return Action[]
     */
    public function createActions (ApplicationContext &$context) {

        return [];

    }

    /**
     * @param $entityName
     *
     * @return ModuleEntity
     */
    public function getModuleEntity ($entityName) {

        if (!isset($this->moduleEntities[$entityName])) {
            throw new \RuntimeException(sprintf('ModuleEntity %s not found', $entityName));
        }

        return $this->moduleEntities[$entityName];

    }

    /**
     * @return string
     */
    public function getActionModuleName () {

        $namespace = (new \ReflectionClass($this))->getNamespaceName();

        return strstr($namespace, '\\', true) . '\\' . $this->getControllerName();

    }

    /**
     * @return string
     */
    public function getControllerName () {

        $namespace = (new \ReflectionClass($this))->getNamespaceName();

        return substr($namespace, strrpos($namespace, '\\') + 1);

    }

} 