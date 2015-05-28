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
     * @param ApplicationContext $appCtx
     *
     * @return Action[]
     */
    public function createActions (ApplicationContext &$appCtx) {

        $studentInfoModuleEntity = $this->getModuleEntity('StudentInfo');

        return [
            new BasicCreateAction('Core\Student', $studentInfoModuleEntity, [], []),
            new BasicUpdateAction('Core\Student', $studentInfoModuleEntity, [], [])
        ];

    }

    /**
     * @param ApplicationContext $applicationContext
     *
     * @return ModuleEntity[]
     */
    public function createModuleEntityDefinitions (ApplicationContext &$applicationContext) {

        return [
            new StudentInfoDefinition()
        ];

    }

}