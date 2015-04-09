<?php


namespace Core\Module\Contact;

use Core\Action\BasicCreateAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Module\ModuleManager as AbstractModuleManager;


class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $context->addAction($a = new BasicCreateAction('Core\Contact', 'Contact', 'ContactHelper', [], [
            'label'    => [new Validation()],
            'streets'  => [new Validation()],
            'city'     => [new Validation()],
            'zip'      => [new Validation()],
            'state'    => [new Validation()],
            'country'  => [new Validation()],
            'email'    => [new Validation()],
            'mobile'   => [new Validation()],
            'landLine' => [new Validation()],
        ], function (ActionContext $context, BasicCreateAction $action) {

            foreach (array_keys($action->getParams()) as $field) {

                if (is_null($context->getParam($field))) {
                    $context->setParam($field, '');
                }

            }

        }));

        $context->addAction(new BasicUpdateAction('Core\Contact', 'Contact', 'ContactHelper', [], [
            'label'    => [new Validation(), true],
            'streets'  => [new Validation(), true],
            'city'     => [new Validation(), true],
            'zip'      => [new Validation(), true],
            'state'    => [new Validation(), true],
            'country'  => [new Validation(), true],
            'email'    => [new Validation(), true],
            'mobile'   => [new Validation(), true],
            'landLine' => [new Validation(), true],
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