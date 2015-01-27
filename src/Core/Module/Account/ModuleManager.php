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

        $context->addAction(new SimpleAction('Core\Account', 'create', NULL, [],
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

        $context->addAction(new SimpleAction('Core\Account', 'update', NULL, [],
            function (ActionContext $context) use ($self) {

                $user = $self->magicalUpdate($context);

                return $user;

            }));

        $context->addAction(new SimpleAction('Core\Account', 'find', NULL, [],
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
    public function loadFields (ApplicationContext &$context) {
        // TODO: Implement loadFields() method.
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