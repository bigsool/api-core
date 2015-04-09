<?php


namespace Core\Module\Student;

use Core\Action\BasicCreateAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ApplicationContext;
use Core\Module\ModuleManager as AbstractModuleManager;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {
        // TODO: Implement loadFilters() method.
    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {
        // TODO: Implement loadRules() method.
    }

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $context->addAction(new BasicCreateAction('Core\Student', 'StudentInfo', 'StudentHelper', [], [
            'schoolName' => [new Validation()],
            'number'     => [new Validation()],
        ]));

        $context->addAction(new BasicUpdateAction('Core\Student', 'StudentInfo', 'StudentHelper', [], [
            'schoolName' => [new Validation(), true],
            'number'     => [new Validation(), true],
        ]));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        $this->addHelper($context, 'StudentHelper');

    }

}