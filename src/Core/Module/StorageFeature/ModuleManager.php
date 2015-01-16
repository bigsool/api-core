<?php

namespace Core\Module\StorageFeature;

use Core\Action\BasicCreateAction;
use Core\Action\BasicFindAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Field\Field;
use Core\Field\StarField;
use Core\Module\ModuleManager as AbstractModuleManager;
use Core\Parameter\SafeParameter;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $context->addAction(new BasicCreateAction('Core\Storage', 'storage', 'StorageFeatureHelper', NULL, [
            'url' => [ERR_INVALID_NAME, new StorageValidation()],
        ], function (ActionContext $context) {

            $context->setParam('login', new SafeParameter(uniqid('login')));
            $context->setParam('password', new SafeParameter(uniqid('password')));
            
        }));

        $context->addAction(new BasicUpdateAction('Core\Storage', 'storage', 'StorageFeatureHelper', NULL, [
            'url' => [ERR_INVALID_NAME, new StorageValidation()],
        ]));

        $context->addAction(new BasicFindAction('Core\Storage', 'storage', 'StorageFeatureHelper', NULL, [
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

        $context->addField(new StarField('Storage'));
        $context->addField(new Field('Storage', 'id'));
        $context->addField(new Field('Storage', 'url'));

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