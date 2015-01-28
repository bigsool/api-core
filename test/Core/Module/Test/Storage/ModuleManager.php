<?php

namespace Core\Module\Test\Storage;

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
            'url' => [ERR_INVALID_NAME, new StorageValidation()],
        ], function (ActionContext $context) {

            $context->setParam('login', uniqid('login'));
            $context->setParam('password', uniqid('password'));

        }));

        $context->addAction(new BasicUpdateAction('Core\TestStorage', 'testStorage', 'StorageFeatureHelper', NULL, [
            'url' => [ERR_INVALID_NAME, new StorageValidation()],
        ]));

        $context->addAction(new BasicFindAction('Core\TestStorage', 'testStorage', 'StorageFeatureHelper', NULL, [
        ]));

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadFields (ApplicationContext &$context) {

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

        $context->addHelper('StorageFeatureHelper', new Helper());

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