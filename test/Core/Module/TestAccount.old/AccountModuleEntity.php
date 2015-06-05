<?php


namespace Core\Module\TestAccount;


use Core\Action\ActionReference;
use Core\Context\ApplicationContext;
use Core\Module\AggregatedModuleEntity;
use Core\Validation\Parameter\Blank;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\Object;

class AccountModuleEntity extends AggregatedModuleEntity {

    public function __construct (ApplicationContext $appCtx) {

        parent::__construct($appCtx, 'Account');

    }

    protected function loadModelAspects (ApplicationContext $applicationContext) {

        $this->setMainEntity([
                                 'model' => 'TestUser',
                             ]);

        $this->addAspect([
                             'model'   => 'TestCompany',
                             'prefix'  => 'company',
                             'keyPath' => 'company',
                             'create'  => [
                                 'constraints' => [new Object(), new NotBlank()],
                             ],
                             'update'  => [
                                 'constraints' => [new Object(), new NotBlank()],
                             ]
                         ]);

        $this->addAspect([
                             'model'   => 'TestStorage',
                             'prefix'  => 'storage',
                             'keyPath' => 'company.storage',
                             'create'  => [
                                 'constraints' => [new Blank()],
                                 'action'      => new ActionReference('TestAccount', 'createStorage')
                             ],
                             'update'  => [
                                 'constraints' => [new Blank()],
                             ]

                         ]);

    }
}