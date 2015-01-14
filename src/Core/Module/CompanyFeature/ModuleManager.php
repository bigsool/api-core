<?php


namespace Core\Module\CompanyFeature;

use Core\Action\SimpleAction as Action;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Expression\BinaryExpression;
use Core\Expression\KeyPath;
use Core\Expression\Parameter;
use Core\Field\Field;
use Core\Field\StarField;
use Core\Filter\ExpressionFilter;
use Core\Filter\FilterReference;
use Core\Filter\StringFilter;
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

        $context->addAction(new Action('Core\Company', 'create', NULL, [
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

        $context->addAction(new Action('Core\Company', 'update', NULL, [
            'id'   => [ERR_INVALID_COMPANY_ID, new CompanyValidation()],
            'name' => [ERR_INVALID_NAME, new CompanyValidation(), true/*force optional*/],
        ], function (ActionContext $context) {

            $params = $context->getVerifiedParams();

            $qryCtx = new FindQueryContext('Company');
            $qryCtx->addKeyPath(new \Core\Field\KeyPath('*'));
            $qryCtx->addFilter(new StringFilter('Company', '', 'id = :id'));
            $qryCtx->setParams(['id' => $params['id']->getValue()]);

            /**
             * @var CompanyFeatureHelper $helper
             */
            $helper = ApplicationContext::getInstance()->getHelper('CompanyFeatureHelper');
            $companies = ApplicationContext::getInstance()->getNewRegistry()->find($qryCtx, false);

            if (count($companies) != 1) {
                throw new \RuntimeException('more or less than one entity found');
            }

            $helper->updateCompany($context, $companies[0], $params);

            return $context['company'];

        }));

        $context->addAction(new Action('Core\Company', 'find', NULL, [
        ], function (ActionContext $context) {

            /**
             * @var CompanyFeatureHelper $helper
             */
            $helper = ApplicationContext::getInstance()->getHelper('CompanyFeatureHelper');
            $reqCtx = $context->getRequestContext();
            $helper->findCompany($context, $reqCtx->getReturnedKeyPaths(), [$reqCtx->getFilter()]);

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