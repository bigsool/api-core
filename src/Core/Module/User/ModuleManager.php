<?php


namespace Core\Module\User;

use Core\Action\Action;
use Core\Action\BasicCreateAction;
use Core\Action\BasicFindAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntity;
use Core\Module\ModuleEntityDefinition;
use Core\Module\ModuleManager as AbstractModuleManager;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $appCtx
     *
     * @return Action[]
     */
    public function createActions (ApplicationContext &$appCtx) {

        $userModuleEntity = $this->getModuleEntity('User');

        return [
            new BasicCreateAction('Core\User', $userModuleEntity, [], ['email' => [\Email, \Optionnal]], function (ActionContext $context) {

                $context->setDefaultParam('lang', $context->getRequestContext()->getLocale());
            }),
            new BasicUpdateAction('Core\User', $userModuleEntity, [], []),
            new BasicFindAction('Core\User', $userModuleEntity, [], [])
        ];

    }

    /**
     * @param ApplicationContext $appCtx
     *
     * @return ModuleEntity[]
     */
    public function createModuleEntityDefinitions (ApplicationContext &$appCtx) {

        return [
            new UserDefinition
        ];

    }

}