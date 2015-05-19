<?php

namespace Core\Module\TestStorage;

use Core\Action\BasicCreateAction;
use Core\Action\BasicFindAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Field\Field;
use Core\Field\StarField;
use Core\Filter\StringFilter;
use Core\Module\GenericDbEntity;
use Core\Module\ModuleManager as AbstractModuleManager;

class ModuleManager extends AbstractModuleManager {

    /**
     * {@inheritDoc}
     */
    public function createActions (ApplicationContext &$appCtx) {

        $testStorageModuleEntity = $this->getModuleEntity('TestStorage');

        return [
            new BasicCreateAction('Core\TestStorage', $testStorageModuleEntity, [], [
                'url' => [new StorageValidation()],
            ], function (ActionContext $context) {

                $context->setParam('login', uniqid('login'));
                $context->setParam('password', uniqid('password'));

            }),
            new BasicUpdateAction('Core\TestStorage', $testStorageModuleEntity, [], [
                'url' => [new StorageValidation()],
            ]),
            new BasicFindAction('Core\TestStorage', $testStorageModuleEntity, [], [
            ])

        ];

    }

    /**
     * {@inheritDoc}
     */
    public function createModuleEntities(ApplicationContext &$context) {

        $storageEntity = new GenericDbEntity($context, 'TestStorage', [
                                                         new StringFilter('TestStorage', 'TestStorageForId', 'id = :id')
                                                     ]
        );

        $storageEntity->setHelper(new Helper($context));

        return [
            $storageEntity
        ];

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        //$this->addHelper($context, 'StorageFeatureHelper');

    }

}