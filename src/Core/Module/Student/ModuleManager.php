<?php


namespace Core\Module\Student;

use Core\Context\ApplicationContext;
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