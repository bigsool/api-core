<?php

namespace Core\Module\TestStorage;

use Core\Action\BasicCreateAction;
use Core\Action\BasicFindAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Field\Field;
use Core\Field\StarField;
use Core\Module\ModuleManager as AbstractModuleManager;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $context->addAction(new BasicCreateAction('Core\TestStorage', 'testStorage', 'StorageFeatureHelper', NULL, [
            'url' => [new StorageValidation()],
        ], function (ActionContext $context) {

            $context->setParam('login', uniqid('login'));
            $context->setParam('password', uniqid('password'));

        }));

        $context->addAction(new BasicUpdateAction('Core\TestStorage', 'testStorage', 'StorageFeatureHelper', NULL, [
            'url' => [new StorageValidation()],
        ]));

        $context->addAction(new BasicFindAction('Core\TestStorage', 'testStorage', 'StorageFeatureHelper', NULL, [
        ]));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFilters (ApplicationContext &$context) {

        $context->addField(new StarField('TestStorage'));
        $context->addField(new Field('TestStorage', 'id'));
        $context->addField(new Field('TestStorage', 'url'));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        $this->addHelper($context, 'StorageFeatureHelper');

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRoutes (ApplicationContext &$context) {

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadRules (ApplicationContext &$context) {

    }

}