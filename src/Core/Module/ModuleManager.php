<?php


namespace Core\Module;


use Core\Context\ApplicationContext;

abstract class ModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function load (ApplicationContext &$context) {

        $this->loadFilters($context);
        $this->loadRules($context);
        $this->loadActions($context);
        $this->loadHelpers($context);
        $this->loadFields($context);

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
     */
    public abstract function loadFilters (ApplicationContext &$context);

    /**
     * @param ApplicationContext $context
     */
    public abstract function loadRules (ApplicationContext &$context);

    /**
     * @param ApplicationContext $context
     */
    public abstract function loadActions (ApplicationContext &$context);

    /**
     * @param ApplicationContext $context
     */
    public abstract function loadHelpers (ApplicationContext &$context);

    /**
     * @param ApplicationContext $context
     */
    public function loadFields (ApplicationContext &$context) {

    }

    public function addHelper (ApplicationContext &$context, $helperName) {

        $namespace = (new \ReflectionClass($this))->getNamespaceName();

        $helper = $namespace . '\\Helper';

        $context->addHelper($this->getActionModuleName(), $helperName, new $helper());

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