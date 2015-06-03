<?php


namespace Core\Module\TestUser;

use Core\Action\BasicCreateAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Filter\StringFilter;
use Core\Module\DbModuleEntity;
use Core\Module\ModuleEntity;
use Core\Module\ModuleManager as AbstractModuleManager;


class ModuleManager extends AbstractModuleManager {

    /**
     * {@inheritDoc}
     */
    public function createActions (ApplicationContext &$appCtx) {

        $testUserModuleEntity = $this->getModuleEntity('TestUser');

        return [
            new BasicCreateAction('Core\TestUser', $testUserModuleEntity, [], [
                'name'      => [new UserValidation()],
                'email'     => [new UserValidation()],
                'firstname' => [new UserValidation()],
                'password'  => [new UserValidation()],
                'knowsFrom' => [new UserValidation()]
            ], function (ActionContext $context) {

                $context->setParam('lang', 'fr');

            }),
            new BasicUpdateAction('Core\TestUser', $testUserModuleEntity, [], [
                'name'      => [new UserValidation()],
                'email'     => [new UserValidation()],
                'firstname' => [new UserValidation()],
                'password'  => [new UserValidation()],
                'knowsFrom' => [new UserValidation()]
            ])
        ];

    }

    /**
     * @param ApplicationContext $context
     *
     * @return ModuleEntity[]
     */
    public function getModuleEntitiesName (ApplicationContext &$context) {

        $testUserModuleEntity = new DbModuleEntity($context, 'TestUser', [
                                                               new StringFilter('TestUser', 'TestUserForId', 'id = :id')
                                                           ]
        );
        $testUserModuleEntity->setHelper(new ModuleEntityHelper($context));

        return [
            $testUserModuleEntity
        ];

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        //$this->addHelper($context, 'UserFeatureHelper');

    }

}