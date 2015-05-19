<?php


namespace Core\Module\Company;

use Core\Action\BasicCreateAction;
use Core\Action\BasicFindAction;
use Core\Action\BasicUpdateAction;
use Core\Context\ApplicationContext;
use Core\Filter\StringFilter;
use Core\Module\GenericDbEntity;
use Core\Module\ModuleManager as AbstractModuleManager;

class ModuleManager extends AbstractModuleManager {

    /**
     * {@inheritDoc}
     */
    public function createActions (ApplicationContext &$appCtx) {

        return [
            new BasicCreateAction('Core\Company', $this->getModuleEntity('Company'), NULL, [
                'name' => [new Validation()],
                'vat'  => [new Validation()],
            ]),
            new BasicUpdateAction('Core\Company', $this->getModuleEntity('Company'), NULL, [
                'name' => [new Validation(), true],
                'vat'  => [new Validation(), true],
            ]),
            new BasicFindAction('Core\Company', $this->getModuleEntity('Company'), NULL, []),
        ];

    }

    public function createModuleEntities (ApplicationContext &$context) {

        return [
            new GenericDbEntity($context, 'Company', [
                                            new StringFilter('Company', 'filterById', 'id = :id')
                                        ]
            )
        ];

    }

    /**
     * @param ApplicationContext $context
     */
    public function loadHelpers (ApplicationContext &$context) {

        // TODO
        // $this->addHelper($context, 'CompanyHelper');

    }

}