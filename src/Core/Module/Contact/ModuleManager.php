<?php


namespace Core\Module\Contact;

use Core\Context\ApplicationContext;
use Core\Module\ModuleManager as AbstractModuleManager;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        $this->addHelper($context, 'ContactHelper');

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {

    }

}