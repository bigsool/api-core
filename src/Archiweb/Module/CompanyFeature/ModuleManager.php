<?php


namespace Archiweb\Module\CompanyFeature;

use Archiweb\Action\SimpleAction as Action;
use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Expression\BinaryExpression;
use Archiweb\Expression\KeyPath;
use Archiweb\Expression\Parameter;
use Archiweb\Field\Field;
use Archiweb\Field\StarField;
use Archiweb\Filter\ExpressionFilter;
use Archiweb\Filter\FilterReference;
use Archiweb\Module\CompanyFeature\Helper as CompanyFeatureHelper;
use Archiweb\Module\ModuleManager as AbstractModuleManager;
use Archiweb\Operator\MemberOf;
use Archiweb\Rule\FieldRule;
use Archiweb\Rule\SimpleRule;
use Archiweb\Validation\CompanyValidation;


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
        $context->addFilter(new ExpressionFilter('Company', 'mee', 'SELECT', $expression));

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