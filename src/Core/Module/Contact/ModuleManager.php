<?php


namespace Core\Module\Contact;

use Core\Action\Action;
use Core\Action\BasicCreateAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ApplicationContext;
use Core\Module\ModuleEntityDefinition;
use Core\Module\ModuleManager as AbstractModuleManager;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $appCtx
     *
     * @return Action[]
     */
    public function createActions (ApplicationContext &$appCtx) {

        $contactModuleEntity = $this->getModuleEntity('Contact');

        return [
            new BasicCreateAction('Core\Contact', $contactModuleEntity, [], []),
            new BasicUpdateAction('Core\Contact', $contactModuleEntity, [], [])
        ];

    }

    /**
     * @param ApplicationContext $context
     *
     * @return ModuleEntityDefinition[]
     */
    public function createModuleEntityDefinitions (ApplicationContext &$context) {

        return [
            new ContactDefinition()
        ];

    }

}