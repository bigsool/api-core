<?php


namespace Core\Module\User2CompanyConnector;

use Core\Action\SimpleAction as Action;
use Core\Auth;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Controller;
use Core\Model\Company;
use Core\Model\User;
use Core\Module\ModuleManager as AbstractModuleManager;
use Core\Validation\CompanyValidation;
use Core\Validation\User2CompanyValidation;
use Symfony\Component\Routing\Route;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $context->addAction(new Action('User2Company', 'create', NULL, [
            'user'    => [ERR_PARAMS_INVALID, new User2CompanyValidation()],
            'company' => [ERR_PARAMS_INVALID, new User2CompanyValidation()],
        ], function (ActionContext $context) {

            $userContext = new ActionContext($context);
            $userContext->setParams($userContext->getParam('user')->getValue());

            $companyContext = new ActionContext($context);
            $companyContext->setParams($userContext->getParam('company')->getValue());

            /**
             * @var User    $user
             * @var Company $company
             */
            $context['user'] =
            $user = ApplicationContext::getInstance()->getAction('User', 'create')->process($userContext);
            $context['company'] =
            $company = ApplicationContext::getInstance()->getAction('Company', 'create')->process($companyContext);

            $user->setCompany($company);
            $company->addUser($user);
            $company->setOwner($user);
            $user->setOwnedCompany($company);

            ApplicationContext::getInstance()->getNewRegistry()->save($user);

            return $user;

        }));

        $context->addAction(new Action('User2Company', 'listUsers', Auth::AUTHENTICATED, [
            'id' => [ERR_INVALID_COMPANY_ID, new CompanyValidation(), true],
        ], function (ActionContext $context) {

            /**
             * @var Helper $helper
             */
            $helper = ApplicationContext::getInstance()->getHelper('User2CompanyHelper');
            $params = $context->getVerifiedParams();

            $helper->listUsers($context, $params);

            return $context['users'];

        }));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFields (ApplicationContext &$context) {

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        $context->addHelper('User2CompanyHelper', new Helper());

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRoutes (ApplicationContext &$context) {

        $context->addRoute('userCreate', new Route('/user/create', [
            'controller' => new Controller('User2Company', 'create')
        ]));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {

    }

}