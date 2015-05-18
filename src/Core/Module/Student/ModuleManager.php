<?php


namespace Core\Module\Student;

use Core\Action\BasicCreateAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ApplicationContext;
use Core\Module\ModuleManager as AbstractModuleManager;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $appCtx
     */
    public function loadActions (ApplicationContext &$appCtx) {

        $appCtx->addAction(new BasicCreateAction('Core\Student', 'StudentInfo', 'StudentHelper', [], [
            'schoolName' => [new Validation()],
            'number'     => [new Validation()],
        ]));

        $appCtx->addAction(new BasicUpdateAction('Core\Student', 'StudentInfo', 'StudentHelper', [], [
            'schoolName' => [new Validation(), true],
            'number'     => [new Validation(), true],
        ]));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {
        // TODO: Implement loadFilters() method.
    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        $this->addHelper($context, 'StudentHelper');

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {
        // TODO: Implement loadRules() method.
    }

}