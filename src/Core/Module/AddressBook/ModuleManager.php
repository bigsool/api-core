<?php


namespace Core\Module\AddressBook;

use Core\Context\ApplicationContext;
use Core\Module\ModuleManager as AbstractModuleManager;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $appCtx
     */
    public function loadActions (ApplicationContext &$appCtx) {
        // TODO: Implement loadActions() method.
    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {
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
    public function loadRules (ApplicationContext &$context) {
        // TODO: Implement loadRules() method.
    }
}