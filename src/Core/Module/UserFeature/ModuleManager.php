<?php


namespace Core\Module\UserFeature;

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

        $context->addAction(new BasicCreateAction('Core\User', 'user', 'UserFeatureHelper', NULL, [
            'name'      => [ERR_INVALID_NAME, new UserValidation()],
            'email'     => [ERR_INVALID_PARAM_EMAIL, new UserValidation()],
            'firstname' => [ERR_PARAMS_INVALID, new UserValidation()],
            'password'  => [ERR_INVALID_PASSWORD, new UserValidation()],
            'knowsFrom' => [ERR_PARAMS_INVALID, new UserValidation()]
        ], function (ActionContext $context) {

            $context->setParam('lang', 'fr');

        }));

        $context->addAction(new BasicUpdateAction('Core\User', 'user', 'UserFeatureHelper', NULL, [
            'name'      => [ERR_INVALID_NAME, new UserValidation()],
            'email'     => [ERR_INVALID_PARAM_EMAIL, new UserValidation()],
            'firstname' => [ERR_PARAMS_INVALID, new UserValidation()],
            'password'  => [ERR_INVALID_PASSWORD, new UserValidation()],
            'knowsFrom' => [ERR_PARAMS_INVALID, new UserValidation()]
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