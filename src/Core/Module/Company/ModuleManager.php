<?php


namespace Core\Module\Company;

use Core\Action\BasicCreateAction;
use Core\Action\BasicFindAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ApplicationContext;
use Core\Filter\StringFilter;
use Core\Module\ModuleManager as AbstractModuleManager;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $context->addAction(new BasicCreateAction('Core\Company', 'company', 'CompanyHelper', NULL, [
            'name' => [new Validation()],
            'vat'  => [new Validation()],
        ]));

        $context->addAction(new BasicUpdateAction('Core\Company', 'company', 'CompanyHelper', NULL, [
            'name' => [new Validation(), true],
            'vat'  => [new Validation(), true],
        ]));

        $context->addAction(new BasicFindAction('Core\Company', 'company', 'CompanyHelper', NULL, [
        ]));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {

        $context->addFilter(new StringFilter('Company', 'filterById', 'id = :id'));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        $this->addHelper($context, 'CompanyHelper');

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {
        // TODO: Implement loadRules() method.
    }

}