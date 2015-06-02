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