<?php


namespace Core\Module\User;

use Core\Action\BasicCreateAction;
use Core\Action\BasicFindAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Filter\StringFilter;
use Core\Module\ModuleManager as AbstractModuleManager;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $appCtx
     */
    public function loadActions (ApplicationContext &$appCtx) {

        $appCtx->addAction(new BasicCreateAction('Core\User', 'user', 'UserHelper', NULL, [
            'lastName'  => [new Validation()],
            'firstName' => [new Validation()],
            'lang'      => [new Validation(), true],
        ], function (ActionContext $context) {

            if ($context->getParam('lang') === NULL) {
                $context->setParam('lang', $context->getRequestContext()->getLocale());
            }

        }));

        $appCtx->addAction(new BasicUpdateAction('Core\User', 'user', 'UserHelper', NULL, [
            'lastName'  => [new Validation(), true],
            'firstName' => [new Validation(), true],
            'lang'      => [new Validation(), true],
        ]));

        $appCtx->addAction(new BasicFindAction('Core\User', 'user', 'UserHelper', NULL, [
        ]));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {

        $context->addFilter(new StringFilter('User', 'UserForId', 'id = :id'));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        $this->addHelper($context, 'UserHelper');

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {

    }

}