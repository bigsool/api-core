<?php

namespace Core\Module\StorageFeature;

use Core\Action\SimpleAction as Action;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Module\ModuleManager as AbstractModuleManager;
use Core\Module\StorageFeature\Helper as StorageFeatureHelper;

class ModuleManager extends AbstractModuleManager {

    /**
     * @param ApplicationContext $context
     */
    public function loadActions (ApplicationContext &$context) {

        $context->addAction(new Action('Core\Storage', 'create', NULL, [
            'name' => [ERR_INVALID_NAME, new StorageValidation()],
        ], function (ActionContext $context) {


            /**
             * @var StorageFeatureHelper $helper
             */
            $helper = ApplicationContext::getInstance()->getHelper('StorageFeatureHelper');
            $params = $context->getVerifiedParams();
            $helper->createStorage($context, $params);

            return $context['storage'];

        }));

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