<?php


namespace Core\Module\Test\User;

use Core\Action\BasicCreateAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Field\Field;
use Core\Field\StarField;
use Core\Module\ModuleManager as AbstractModuleManager;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $context->addAction(new BasicCreateAction('Core\TestUser', 'testUser', 'UserFeatureHelper', NULL, [
            'name'      => [new UserValidation()],
            'email'     => [new UserValidation()],
            'firstname' => [new UserValidation()],
            'password'  => [new UserValidation()],
            'knowsFrom' => [new UserValidation()]
        ], function (ActionContext $context) {

            $context->setParam('lang', 'fr');

        }));

        $context->addAction(new BasicUpdateAction('Core\TestUser', 'testUser', 'UserFeatureHelper', NULL, [
            'name'      => [new UserValidation()],
            'email'     => [new UserValidation()],
            'firstname' => [new UserValidation()],
            'password'  => [new UserValidation()],
            'knowsFrom' => [new UserValidation()]
        ]));

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

        $context->addHelper('UserFeatureHelper', new Helper());

    }

    public function loadRoutes (ApplicationContext &$context) {

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {

    }

}