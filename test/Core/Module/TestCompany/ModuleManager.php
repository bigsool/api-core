<?php


namespace Core\Module\TestCompany;

use Core\Action\BasicCreateAction;
use Core\Action\BasicFindAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ApplicationContext;
use Core\Expression\BinaryExpression;
use Core\Expression\KeyPath;
use Core\Expression\Parameter;
use Core\Filter\ExpressionFilter;
use Core\Module\DbModuleEntity;
use Core\Module\ModuleManager as AbstractModuleManager;
use Core\Operator\MemberOf;


class ModuleManager extends AbstractModuleManager {

    /**
     * {@inheritDoc}
     */
    public function createActions (ApplicationContext &$appCtx) {

        $testCompanyModuleEntity = $this->getModuleEntity('TestCompany');

        return [
            new BasicCreateAction('Core\TestCompany', $testCompanyModuleEntity, NULL, [
                'name' => [new CompanyValidation()],
            ]),
            new BasicUpdateAction('Core\TestCompany', $testCompanyModuleEntity, NULL, [
                'name' => [new CompanyValidation()],
            ]),
            new BasicFindAction('Core\TestCompany', $testCompanyModuleEntity, NULL, [
            ]),

        ];

    }

    /**
     * {@inheritDoc}
     */
    public function createModuleEntityDefinitions (ApplicationContext &$context) {

        return [
            new DbModuleEntity($context, 'TestCompany')
        ];

    }

    /**
     * {@inheritDoc}
     */
    public function createModuleFilters (ApplicationContext &$context) {

        $expression = new BinaryExpression(new MemberOf(), new Parameter(':authUser'), new KeyPath('users'));

        return [
            new ExpressionFilter('TestCompany', 'mee', $expression)
        ];

    }

    /**
     * {@inheritDoc}
     */
    public function createRules (ApplicationContext &$context) {

        return [
            //new FieldRule(new StarField('TestCompany'), new FilterReference($context, 'TestCompany', 'mee'))
        ];

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        //$this->addHelper($context, 'CompanyFeatureHelper');

    }

    public function loadRoutes (ApplicationContext &$context) {

    }

}