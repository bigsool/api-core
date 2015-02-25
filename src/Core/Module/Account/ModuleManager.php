<?php

namespace Core\Module\Account;


use Core\Action\SimpleAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Model\Account;
use Core\Model\Company;
use Core\Module\MagicalModuleManager;
use Core\Validation\Constraints\Dictionary;
use Symfony\Component\Validator\Constraints\Blank;
use Symfony\Component\Validator\Constraints\NotBlank;

class ModuleManager extends MagicalModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $self = $this;

        /**
         * @api            {post} /account/create
         * @apiVersion     1.0.0
         * @apiDescription Create an account.
         *                 It could be a regular user, a sub user or a student depending on the parameter type
         *
         * @apiSuccess {string} email The Account email
         * @apiSuccess {int}    id    The Account id
         * @apiSuccess {Object} company The company object
         * @apiSuccess {int} company.id The company id for this newly created account
         * @apiSuccessExample {json}
         * HTTP 1/1 200 OK
         * {
         *   "id":123,
         *   "email":"julien@bigsool.com",
         *   "company":{
         *       "id":456,
         *       "name":"Bigsool"
         *   }
         * }
         *
         * @apiError       101 Invalid parameter Email
         * @apiError       102 Invalid parameter Name
         * @apiError       1002 Email already used
         */
        // TODO: handle HTTP METHOD (POST,GET,PUT,DELETE) (REST CRUD)
        $this->addRoute('/account/create', 'create');
        $context->addAction(new SimpleAction($this->getActionModuleName(), 'create', NULL, [],
            function (ActionContext $context) use ($self) {

                /**
                 * @var Account $account
                 */
                $account = $self->magicalCreate($context);

                // The creator of the Account is the owner of the company
                $account->getUser()->setOwnedCompany($account->getCompany());
                $account->getCompany()->setOwner($account->getUser());
                ApplicationContext::getInstance()->getNewRegistry()->save($account);

                return $account;

            }));

        $this->addRoute('/account/create', 'update');
        $context->addAction(new SimpleAction($this->getActionModuleName(), 'update', NULL, [],
            function (ActionContext $context) use ($self) {

                $user = $self->magicalUpdate($context);

                return $user;

            }));

        $this->addRoute('/account/create', 'find');
        $context->addAction(new SimpleAction($this->getActionModuleName(), 'find', NULL, [],
            function (ActionContext $context) use ($self) {

                throw new \RuntimeException('Not implemented yet');

            }));

    }

    public function loadAspects () {

        $this->setMainEntity([
                                 'model' => 'User',
                             ]);

        $this->addAspect([
                             'model'   => 'Company',
                             'prefix'  => 'company',
                             'keyPath' => 'company',
                             'create'  => [
                                 'constraints' => [new Dictionary(), new NotBlank()],
                             ],
                             'update'  => [
                                 'constraints' => [new Dictionary(), new NotBlank()],
                             ]
                         ]);

        $this->addAspect([
                             'model'   => 'Storage',
                             'prefix'  => 'storage',
                             'keyPath' => 'company.storage',
                             'create'  => [
                                 'constraints' => [new Blank()],
                                 'action'      => $this->getCreateStorageAction()
                             ],
                             'update'  => [
                                 'constraints' => [new Blank()],
                             ]

                         ]);
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
    public function loadRoutes (ApplicationContext &$context) {
        // TODO: Implement loadRoutes() method.
    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {
        // TODO: Implement loadRules() method.
    }

    protected function getCreateStorageAction () {

        $self = $this;

        return new SimpleAction('Account', 'createStorage', [], [], function (ActionContext $context) use (&$self) {

            $company = $context['company'];
            if (!($company instanceof Company)) {
                throw new \RuntimeException('company must be defined in the context');
            }

            $context->setParams(['url' => $company->getId() . '-' . $company->getName()]);

            return $self->getMagicalAction('create', $self->getModelAspectForModelName('Storage'))->process($context);

        });

    }

}