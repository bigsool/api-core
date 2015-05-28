<?php


namespace Core\Module\Marketing;

use Core\Action\Action;
use Core\Action\BasicCreateAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ApplicationContext;
use Core\Module\DbModuleEntity;
use Core\Module\ModuleEntity;
use Core\Module\ModuleManager as AbstractModuleManager;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $appCtx
     *
     * @return Action[]
     */
    public function createActions (ApplicationContext &$appCtx) {

        $marketingInfoModuleEntity = $this->getModuleEntity('MarketingInfo');

        return [
            new BasicCreateAction('Core\Marketing', $marketingInfoModuleEntity, [], []),
            new BasicUpdateAction('Core\Marketing', $marketingInfoModuleEntity, [], [])
        ];

    }

    /**
     * @param ApplicationContext $context
     *
     * @return ModuleEntity[]
     */
    public function createModuleEntityDefinitions (ApplicationContext &$context) {

        return [
            new MarketingInfoDefinition()
        ];

    }

}