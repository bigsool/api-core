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
        $this->loadRoutes($context);
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
    public abstract function loadRoutes (ApplicationContext &$context);

    /**
     * @param ApplicationContext $context
     */
    public abstract function loadHelpers (ApplicationContext &$context);

} 