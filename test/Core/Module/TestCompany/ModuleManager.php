<?php


namespace Core\Module\TestCompany;

use Core\Action\BasicCreateAction;
use Core\Action\BasicFindAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ApplicationContext;
use Core\Expression\BinaryExpression;
use Core\Expression\KeyPath;
use Core\Expression\Parameter;
use Core\Field\StarField;
use Core\Filter\ExpressionFilter;
use Core\Filter\FilterReference;
use Core\Module\ModuleManager as AbstractModuleManager;
use Core\Operator\MemberOf;
use Core\Rule\FieldRule;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $appCtx
     */
    public function loadActions (ApplicationContext &$appCtx) {

        $appCtx->addAction(new BasicCreateAction('Core\TestCompany', 'testCompany', 'CompanyFeatureHelper', NULL, [
            'name' => [new CompanyValidation()],
        ]));

        $appCtx->addAction(new BasicUpdateAction('Core\TestCompany', 'testCompany', 'CompanyFeatureHelper', NULL, [
            'name' => [new CompanyValidation()],
        ]));

        $appCtx->addAction(new BasicFindAction('Core\TestCompany', 'testCompany', 'CompanyFeatureHelper', NULL, [
        ]));

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

        $this->addHelper($context, 'CompanyFeatureHelper');

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