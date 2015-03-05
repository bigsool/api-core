<?php


namespace Core\Module\Company;

use Core\Action\BasicCreateAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Module\ModuleManager as AbstractModuleManager;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $context->addAction(new BasicCreateAction('Core\Company', 'company', 'CompanyFeatureHelper', NULL, [
            'name' => [new Validation()],
            'vat'  => [new Validation()],
        ], function (ActionContext $context) {

        }));

        $context->addAction(new BasicUpdateAction('Core\Company', 'company', 'CompanyFeatureHelper', NULL, [
            'name' => [new Validation()],
            'vat'  => [new Validation()],
        ], function (ActionContext $context) {

        }));

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
        // TODO: Implement loadHelpers() method.
    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {
        // TODO: Implement loadRules() method.
    }

}