<?php


namespace Core\Module\CompanyFeature;

use Core\Action\SimpleAction as Action;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Expression\BinaryExpression;
use Core\Expression\KeyPath;
use Core\Expression\Parameter;
use Core\Field\Field;
use Core\Field\StarField;
use Core\Filter\ExpressionFilter;
use Core\Filter\FilterReference;
use Core\Module\CompanyFeature\Helper as CompanyFeatureHelper;
use Core\Module\ModuleManager as AbstractModuleManager;
use Core\Operator\MemberOf;
use Core\Rule\FieldRule;
use Core\Validation\CompanyValidation;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $context->addAction(new Action('Company', 'create', NULL, [
            'name' => [ERR_INVALID_NAME, new CompanyValidation()],
        ], function (ActionContext $context) {


            /**
             * @var CompanyFeatureHelper $helper
             */
            $helper = ApplicationContext::getInstance()->getHelper('CompanyFeatureHelper');
            $params = $context->getVerifiedParams();
            $helper->createCompany($context, $params);

            return $context['company'];

        }));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFields (ApplicationContext &$context) {

        $context->addField(new StarField('Company'));
        $context->addField(new Field('Company', 'id'));
        $context->addField(new Field('Company', 'name'));
        $context->addField(new StarField('Storage'));
    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {

        $expression = new BinaryExpression(new MemberOf(), new Parameter(':authUser'), new KeyPath('users'));
        $context->addFilter(new ExpressionFilter('Company', 'mee', $expression));

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

        $context->addRule(new FieldRule(new StarField('Company'), new FilterReference($context, 'Company', 'mee')));

    }

}