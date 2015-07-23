<?php


namespace Core\Module\Contact;

use Core\Context\ApplicationContext;
use Core\Module\ModuleEntityDefinition;
use Core\Module\ModuleManager as AbstractModuleManager;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     *
     * @return ModuleEntityDefinition[]
     */
    public function getModuleEntitiesName (ApplicationContext &$context) {

        return [
            'Contact'
        ];

    }

}