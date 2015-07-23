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
     * @return string
     */
    public static function getNamespace () {

        $moduleManagerClassName = get_called_class();

        return substr($moduleManagerClassName, 0, strrpos($moduleManagerClassName, '\\') + 1);

    }

    /**
     * @param ApplicationContext $context
     *
     * @return string[]
     */
    public function getModuleEntitiesName (ApplicationContext &$context) {

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
     * @return ModuleEntity[]
     */
    public function getModuleEntities () {

        return array_values($this->moduleEntities);

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

    /**
     * @param ModuleEntity $moduleEntity
     */
    public function addModuleEntity (ModuleEntity $moduleEntity) {

        $entityName = $moduleEntity->getDefinition()->getEntityName();
        if (array_key_exists($entityName, $this->moduleEntities)) {
            throw new \RuntimeException('ModuleEntity already added');
        }

        $this->moduleEntities[$entityName] = $moduleEntity;

    }

} 