<?php


namespace Core\Module\Company;

use Core\Action\Action;
use Core\Action\BasicCreateAction;
use Core\Action\BasicFindAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ApplicationContext;
use Core\Module\ModuleEntityDefinition;
use Core\Module\ModuleManager as AbstractModuleManager;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     *
     * @return ModuleEntityDefinition[]
     */
    public function createModuleEntityDefinitions (ApplicationContext &$context) {

        return [
            new CompanyDefinition()
        ];

    }

}