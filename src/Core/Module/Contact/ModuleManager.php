<?php


namespace Core\Module\Contact;

use Core\Action\BasicCreateAction;
use Core\Action\BasicUpdateAction;
use Core\Action\SimpleAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Module\ModuleManager as AbstractModuleManager;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $context->addAction(new BasicCreateAction('Core\Contact', 'create','ContactHelper', [], [
            'label'    => [new Validation()],
            'streets'  => [new Validation()],
            'city'     => [new Validation()],
            'zip'      => [new Validation()],
            'state'    => [new Validation()],
            'country'  => [new Validation()],
            'email'    => [new Validation()],
            'mobile'   => [new Validation()],
            'landLine' => [new Validation()],
        ]));

        $context->addAction(new BasicUpdateAction('Core\Contact', 'create','ContactHelper', [], [
            'label'    => [new Validation()],
            'streets'  => [new Validation()],
            'city'     => [new Validation()],
            'zip'      => [new Validation()],
            'state'    => [new Validation()],
            'country'  => [new Validation()],
            'email'    => [new Validation()],
            'mobile'   => [new Validation()],
            'landLine' => [new Validation()],
        ]));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        $this->addHelper($context, 'ContactHelper');

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {

    }

}