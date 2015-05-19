<?php


namespace Core\Module\AddressBook;

use Core\Context\ApplicationContext;
use Core\Module\ModuleManager as AbstractModuleManager;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $appCtx
     */
    public function createActions (ApplicationContext &$appCtx) {
        // TODO: Implement loadActions() method.
    }

    /**
     * @param ApplicationContext $context
     */
    public function createModuleFilters (ApplicationContext &$context) {
        // TODO: Implement loadFilters() method.
    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        $this->addHelper($context, 'AddressBookHelper');

    }

    /**
     * @param ApplicationContext $context
     */
    public function createRules (ApplicationContext &$context) {
        // TODO: Implement loadRules() method.
    }
}