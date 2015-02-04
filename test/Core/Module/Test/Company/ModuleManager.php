<?php


namespace Core\Module\Test\Company;

use Core\Action\BasicCreateAction;
use Core\Action\BasicFindAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ApplicationContext;
use Core\Expression\BinaryExpression;
use Core\Expression\KeyPath;
use Core\Expression\Parameter;
use Core\Field\Field;
use Core\Field\StarField;
use Core\Filter\ExpressionFilter;
use Core\Filter\FilterReference;
use Core\Module\ModuleManager as AbstractModuleManager;
use Core\Operator\MemberOf;
use Core\Rule\FieldRule;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $context->addAction(new BasicCreateAction('Core\TestCompany', 'testCompany', 'CompanyFeatureHelper', NULL, [
            'name' => [ERR_INVALID_NAME, new CompanyValidation()],
        ]));

        $context->addAction(new BasicUpdateAction('Core\TestCompany', 'testCompany', 'CompanyFeatureHelper', NULL, [
            'name' => [ERR_INVALID_NAME, new CompanyValidation()],
        ]));

        $context->addAction(new BasicFindAction('Core\TestCompany', 'testCompany', 'CompanyFeatureHelper', NULL, [
        ]));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFields (ApplicationContext &$context) {

        $context->addField(new StarField('TestCompany'));
        $context->addField(new Field('TestCompany', 'id'));
        $context->addField(new Field('TestCompany', 'name'));
        $context->addField(new StarField('TestStorage'));
    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {

        $expression = new BinaryExpression(new MemberOf(), new Parameter(':authUser'), new KeyPath('users'));
        $context->addFilter(new ExpressionFilter('TestCompany', 'mee', $expression));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        $context->addHelper('CompanyFeatureHelper', new Helper());

    }

    public function loadRoutes (ApplicationContext &$context) {

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {

        $context->addRule(new FieldRule(new StarField('TestCompany'),
                                        new FilterReference($context, 'TestCompany', 'mee')));

    }

}