<?php


namespace Archiweb\Module\CompanyFeature;

use Archiweb\Action\SimpleAction as Action;
use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Context\FindQueryContext;
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
        $context->addField(new Field('Company', 'name'));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {

        $expression = new BinaryExpression(new MemberOf(), new Parameter(':authUser'), new KeyPath('company.users'));
        $context->addFilter(new ExpressionFilter('Company', 'me', 'SELECT', $expression));

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

        $context->addRule(new SimpleRule('CompanyMeRule', function (FindQueryContext $context) {

            if ($context instanceof FindQueryContext) {

                return $context->getEntity() == 'Company' || in_array('Company', $context->getJoinedEntities());

            }

        }, new FilterReference($context, 'Company', 'me')));

    }

}