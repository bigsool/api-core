<?php


namespace Archiweb\Module;


use Archiweb\Context\ApplicationContext;

abstract class ModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function load (ApplicationContext &$context) {

        $this->loadFields($context);
        $this->loadFilters($context);
        $this->loadRules($context);
        $this->loadActions($context);
        $this->loadRoutes($context);

    }

    /**
     * @param ApplicationContext $context
     */
    public abstract function loadFields (ApplicationContext &$context);

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

} 