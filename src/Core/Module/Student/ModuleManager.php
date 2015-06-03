<?php


namespace Core\Module\Student;

use Core\Action\Action;
use Core\Action\BasicCreateAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ApplicationContext;
use Core\Module\DbModuleEntity;
use Core\Module\ModuleEntity;
use Core\Module\ModuleManager as AbstractModuleManager;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $applicationContext
     *
     * @return ModuleEntity[]
     */
    public function getModuleEntitiesName (ApplicationContext &$applicationContext) {

        return [
            'StudentInfo'
        ];

    }

}