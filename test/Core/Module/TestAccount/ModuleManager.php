<?php

namespace Core\Module\TestAccount;


use Core\Action\Action;
use Core\Action\SimpleAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Model\TestAccount;
use Core\Model\TestCompany;
use Core\Module\MagicalModuleManager;
use Core\Module\ModuleEntity;

class ModuleManager extends MagicalModuleManager {

    /**
     * @param ApplicationContext $appCtx
     *
     * @return Action[]
     */
    public function createActions (ApplicationContext &$appCtx) {

        /**
         * @var AccountModuleEntity $testAccountModuleEntity
         */
        $testAccountModuleEntity = $this->getModuleEntity('TestUser');

        return [
            new SimpleAction('Core\Account', 'create', [], [],
                function (ActionContext $context) use ($testAccountModuleEntity) {

                    /**
                     * @var TestAccount $account
                     */
                    $account = $testAccountModuleEntity->create($context, $context->getVerifiedParams());

                    // The creator of the TestAccount is the owner of the company
                    $account->getUser()->setOwnedCompany($account->getCompany());
                    $account->getCompany()->setOwner($account->getUser());

                    $testAccountModuleEntity->save($account);

                    return $account;

                }),
            new SimpleAction('Core\Account', 'update', [], [],
                function (ActionContext $context) use ($testAccountModuleEntity) {

                    $account = $testAccountModuleEntity->update($context, $context->getVerifiedParams());

                    return $account;

                }),
            new SimpleAction('Core\Account', 'find', [], [],
                function (ActionContext $context) {

                    throw new \RuntimeException('Not implemented yet');

                }),
            new SimpleAction('TestAccount', 'createStorage', [], [],
                function (ActionContext $context) use ($testAccountModuleEntity) {

                    $company = $context['company'];
                    if (!($company instanceof TestCompany)) {
                        throw new \RuntimeException('company must be defined in the context');
                    }

                    $context->setParams(['url' => $company->getId() . '-' . $company->getName()]);

                    $storageAspect = $testAccountModuleEntity->getModelAspect('TestStorage');

                    return $testAccountModuleEntity->getMagicalAction('create', $storageAspect)->process($context);

                })
        ];

    }

    /**
     * @param ApplicationContext $context
     *
     * @return ModuleEntity[]
     */
    public function createModuleEntityDefinitions (ApplicationContext &$context) {

        return [
            new AccountModuleEntity($context)
        ];

    }

}