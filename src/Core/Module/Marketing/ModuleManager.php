<?php


namespace Core\Module\Marketing;

use Core\Context\ApplicationContext;
use Core\Module\ModuleEntity;
use Core\Module\ModuleManager as AbstractModuleManager;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     *
     * @return ModuleEntity[]
     */
    public function getModuleEntitiesName (ApplicationContext &$context) {

        return [
            'MarketingInfo'
        ];

    }

}