<?php


namespace Core\Module\User;

use Core\Context\ApplicationContext;
use Core\Module\ModuleEntity;
use Core\Module\ModuleManager as AbstractModuleManager;


class ModuleManager extends AbstractModuleManager {

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