<?php


namespace Core\Module\User;

use Core\Action\BasicCreateAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Module\ModuleManager as AbstractModuleManager;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $context->addAction(new BasicCreateAction('Core\User', 'user', 'UserFeatureHelper', NULL, [
            'lastName'  => [new UserValidation()],
            'firstName' => [new UserValidation()],
            'lang'      => [new UserValidation(), true],
        ], function (ActionContext $context) {

            if ($context->getParam('lang') === NULL) {
                $context->setParam('lang', $context->getRequestContext()->getLocale());
            }

        }));

        $context->addAction(new BasicUpdateAction('Core\User', 'user', 'UserFeatureHelper', NULL, [
            'lastName'  => [new UserValidation()],
            'firstName' => [new UserValidation()],
            'lang'      => [new UserValidation()],
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

        $this->addHelper($context, 'UserFeatureHelper');

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {

    }

}