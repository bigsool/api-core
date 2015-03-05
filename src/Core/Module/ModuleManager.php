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
     * @param string $path
     * @param string $action
     */
    public function addRoute ($path, $action) {

        ApplicationContext::getInstance()->addRoute($path, $this->getControllerName(), $action);

    }

    /**
     * @return string
     */
    public function getControllerName () {

        $namespace = (new \ReflectionClass($this))->getNamespaceName();

        return substr($namespace, strrpos($namespace, '\\') + 1);

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

} 