<?php


namespace Core\Module;


use Core\Action\ActionReference;
use Core\Action\GenericAction;
use Core\Action\SimpleAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\RequestContext;
use Core\Field\RelativeField;
use Core\Filter\StringFilter;
use Core\Model\TestAccount;
use Core\Model\TestCompany;
use Core\Model\TestStorage;
use Core\Model\TestUser;
use Core\Model\User;
use Core\Module\TestCompany\ModuleManager as CompanyModuleManager;
use Core\Module\TestStorage\ModuleManager as StorageModuleManager;
use Core\Module\TestUser\Helper as UserHelper;
use Core\Module\TestUser\ModuleManager as UserModuleManager;
use Core\Parameter\UnsafeParameter;
use Core\Registry;
use Core\TestCase;
use Core\Validation\Parameter\Choice;
use Core\Validation\Parameter\DateTime;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\Null;
use Core\Validation\Parameter\Object;
use Core\Validation\RuntimeConstraintsProvider;
use Doctrine\ORM\EntityManager;

class MagicalModuleManagerTest extends TestCase {

    /**
     * @var Connection
     */
    protected static $doctrineConnectionSettings;

    /**
     * @var \Core\Model\TestCompany
     */
    private static $company1;

    /**
     * @var \Core\Model\TestUser
     */
    private static $user1;

    /**
     * @var \Core\Model\TestUser
     */
    private static $user2;

    /**
     * @var \Core\Model\TestUser
     */
    private static $user3;

    /**
     * @var \Core\Model\TestStorage
     */
    private static $storage;

    public function testMinimalAddAspect () {

        $mgr = $this->getMockMagicalModuleManager();
        $this->addUserAspect($mgr);

        $modelAspects = $this->getAspects($mgr);
        $this->assertInternalType('array', $modelAspects);
        $this->assertContainsOnlyInstancesOf('\Core\Module\ModelAspect', $modelAspects);
        $this->assertCount(1, $modelAspects);

        $modelAspect = $modelAspects[0];
        $this->assertNull($modelAspect->getPrefix());
        $this->assertSame('TestUser', $modelAspect->getModel());
        $this->assertSame([], $modelAspect->getConstraints());
        $this->assertNull($modelAspect->getRelativeField());

    }

    protected function addUserAspect (MagicalModuleManager &$mgr) {

        $this->addAspect($mgr, [
            'model' => 'TestUser',
        ]);

    }

    /**
     * @param MagicalModuleManager $mgr
     * @param array                $params
     */
    protected function addAspect (MagicalModuleManager &$mgr, array $params) {

        $method = (new \ReflectionClass($mgr))->getMethod('addAspect');
        $method->setAccessible(true);

        $method->invokeArgs($mgr, [$params]);

    }

    /**
     * @param MagicalModuleManager $mgr
     *
     * @return ModelAspect[]
     */
    protected function getAspects (MagicalModuleManager &$mgr) {

        $method = (new \ReflectionClass($mgr))->getMethod('getAspects');
        $method->setAccessible(true);

        return $method->invoke($mgr);

    }

    /**
     * @expectedException \Exception
     */
    public function testAddAspectInvalidModel () {

        $mgr = $this->getMockMagicalModuleManager();
        $this->addAspect($mgr, [
            'model' => 'qwe',
        ]);

    }

    /**
     * @expectedException \Exception
     */
    public function testAddAspectInvalidPrefix () {

        $mgr = $this->getMockMagicalModuleManager();
        $this->addAspect($mgr, [
            'model'  => 'TestUser',
            'prefix' => new \stdClass(),
        ]);

    }

    /**
     * @expectedException \Exception
     */
    public function testAddAspectInvalidKeyPath () {

        $mgr = $this->getMockMagicalModuleManager();
        $this->addAspect($mgr, [
            'model'   => 'TestUser',
            'keyPath' => 'qwe qwe',
        ]);

    }

    /**
     * @expectedException \Exception
     */
    public function testAddAspectInvalidConstraints () {

        $mgr = $this->getMockMagicalModuleManager();
        $this->addAspect($mgr, [
            'model'  => 'TestUser',
            'create' => [
                'constraints' => [new \stdClass()],
            ]
        ]);

    }

    /**
     * @expectedException \Exception
     */
    public function testAddAspectInvalidActions () {

        $mgr = $this->getMockMagicalModuleManager();
        $this->addAspect($mgr, [
            'model'  => 'TestUser',
            'create' => [
                'action' => new \stdClass(),
            ]
        ]);

    }

    /**
     * @expectedException \Exception
     */
    public function testAddTwoMainEntitiesAspect () {

        $mgr = $this->getMockMagicalModuleManager();
        $this->addAspect($mgr, [
            'model' => 'TestUser',
        ]);
        $this->addAspect($mgr, [
            'model' => 'TestCompany',
        ]);

    }

    public function testComplexAddAspect () {

        $mgr = $this->getMockMagicalModuleManager();
        $this->addUserAspect($mgr);
        $this->addCompanyAspect($mgr);
        $this->addStorageAspect($mgr);

        $modelAspects = $this->getAspects($mgr);
        $this->assertInternalType('array', $modelAspects);
        $this->assertContainsOnlyInstancesOf('\Core\Module\ModelAspect', $modelAspects);
        $this->assertCount(3, $modelAspects);

        $modelAspect = $modelAspects[0];
        $this->assertNull($modelAspect->getPrefix());
        $this->assertSame('TestUser', $modelAspect->getModel());
        $this->assertSame([], $modelAspect->getConstraints());
        $this->assertNull($modelAspect->getRelativeField());

        $modelAspect = $modelAspects[1];
        $this->assertSame('firm', $modelAspect->getPrefix());
        $this->assertSame('TestCompany', $modelAspect->getModel());
        $keyPath = $modelAspect->getRelativeField();
        $this->assertInstanceOf('\Core\Field\RelativeField', $keyPath);
        $this->assertSame('company', $keyPath->getValue());
        $validators = $modelAspect->getConstraints('create');
        $this->assertInternalType('array', $validators);
        $this->assertContainsOnlyInstancesOf('\Core\Validation\Parameter\Constraint', $validators);
        $this->assertCount(2, $validators);
        $this->assertInstanceOf('\Core\Validation\Parameter\Object', $validators[0]);
        $this->assertInstanceOf('\Core\Validation\Parameter\NotBlank', $validators[1]);

        $modelAspect = $modelAspects[2];
        $this->assertSame('s3', $modelAspect->getPrefix());
        $this->assertSame('TestStorage', $modelAspect->getModel());
        $keyPath = $modelAspect->getRelativeField();
        $this->assertInstanceOf('\Core\Field\RelativeField', $keyPath);
        $this->assertSame('company.storage', $keyPath->getValue());
        $validators = $modelAspect->getConstraints('create');
        $this->assertInternalType('array', $validators);
        $this->assertContainsOnlyInstancesOf('\Core\Validation\Parameter\Constraint', $validators);
        $this->assertCount(1, $validators);
        $this->assertInstanceOf('\Core\Validation\Parameter\Null', $validators[0]);

    }

    /**
     * @param MagicalModuleManager $mgr
     */
    protected function addCompanyAspect (MagicalModuleManager &$mgr) {

        $this->addAspect($mgr, [
            'model'   => 'TestCompany',
            'prefix'  => 'firm',
            'keyPath' => 'company',
            'create'  => [
                'constraints' => [new Object(), new NotBlank()],
            ],
            'update'  => [
                'constraints' => [new Object(), new NotBlank()],
            ]
        ]);

    }

    /**
     * @param MagicalModuleManager $mgr
     */
    protected function addStorageAspect (MagicalModuleManager &$mgr) {

        $this->addAspect($mgr, [
            'model'   => 'TestStorage',
            'prefix'  => 's3',
            'keyPath' => 'company.storage',
            'create'  => [
                'constraints' => [new Null()],
                'action'      => NULL,
            ]
        ]);

    }

    public function testParamsOfActionContexts () {

        $userCalled = $companyCalled = $storageCalled = false;

        $mgr = $this->getMockMagicalModuleManager();
        $this->setMainEntity($mgr, [
            'model'  => 'TestUser',
            'create' => [
                'action' => new GenericAction('module', 'name', function (ActionContext $context) use (&$userCalled) {

                    $this->assertSame(['firstName' => 'first name'], $context->getParams());
                    $userCalled = true;

                }, $this->getCallable(), $this->getCallable())
            ]
        ]);
        $this->addAspect($mgr, [
            'model'   => 'TestCompany',
            'prefix'  => 'company',
            'keyPath' => 'company',
            'create'  => [
                'action' => new GenericAction('module', 'name',
                    function (ActionContext $context) use (&$companyCalled) {

                        $this->assertSame(['name' => 'company'], $context->getParams());
                        $companyCalled = true;

                    }, $this->getCallable(), $this->getCallable())
            ]
        ]);
        $this->addAspect($mgr, [
            'model'   => 'TestStorage',
            'prefix'  => 'storage',
            'keyPath' => 'company.storage',
            'create'  => [
                'action' => new GenericAction('module', 'name',
                    function (ActionContext $context) use (&$storageCalled) {

                        $this->assertSame([], $context->getParams());
                        $storageCalled = true;

                    }, $this->getCallable(), $this->getCallable())
            ]
        ]);
        $context = new ActionContext(new RequestContext());
        $context->setParams(['firstName' => 'first name', 'company.name' => 'company']);
        $mgr->magicalCreate($context);

        $this->assertTrue($userCalled);
        $this->assertTrue($companyCalled);
        $this->assertTrue($storageCalled);

    }

    /**
     * @param MagicalModuleManager $mgr
     * @param array                $params
     */
    protected function setMainEntity (MagicalModuleManager &$mgr, array $params) {

        $method = (new \ReflectionClass($mgr))->getMethod('setMainEntity');
        $method->setAccessible(true);

        $method->invokeArgs($mgr, [$params]);

    }

    public function AddAspectOneToMany () {

        $mgr = $this->getMockMagicalModuleManager();

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();

        $this->addAspect($mgr, [
            'model' => 'TestCompany',
        ]);
        $this->addAspect($mgr, [
            'model'   => 'TestUser',
            'keyPath' => 'users',
            'prefix'  => 'users',
        ]);


        $appCtx = ApplicationContext::getInstance();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);
        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);

        $actionContext = $this->getActionContextWithParams(
            [
                'name'  => new UnsafeParameter('qwe SA', ''),
                'users' => new UnsafeParameter([
                                                   [
                                                       'email'    => 'qwe@qwe.com',
                                                       'name'     => 'thierry',
                                                       'password' => 'qwe',
                                                   ],
                                                   [
                                                       'email'    => 'qwe2@qwe.com',
                                                       'name'     => 'thierry2',
                                                       'password' => 'qwe2',
                                                   ]
                                               ], '')
            ]);
        $mgr->magicalCreate($actionContext);
    }

    /**
     * @param array $params
     *
     * @return ActionContext
     */
    protected function getActionContextWithParams (array $params) {

        $actionContext = new ActionContext(new RequestContext());
        $actionContext->setParams($params);

        return $actionContext;

    }

    public function testSimpleDefineAction () {

        $appCtx = ApplicationContext::getInstance();
        $appCtx->setProduct('Archipad');

        $called = false;

        $self = $this;

        $processFn = function (ActionContext $ctx) use (&$called, &$self) {

            $self->assertCount(0, $ctx->getParams());
            $called = true;

        };

        $mgr = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgr->method('getModuleName')->willReturn('ModuleName');
        $this->addUserAspect($mgr);
        $this->defineAction($mgr, ['qwe', [], $processFn]);

        $actions = $appCtx->getActions();
        $this->assertCount(1, $actions);
        $action = $actions[0];
        $this->assertSame('qwe', $action->getName());
        $this->assertSame('ModuleName', $action->getModule());

        $actionContext = $this->getMockActionContext();
        $actionContext->method('getParams')->willReturn([]);

        $action->process($actionContext);
        $this->assertTrue($called);

    }

    /**
     * @param MagicalModuleManager $mgr
     * @param array                $args
     */
    protected function defineAction (MagicalModuleManager &$mgr, array $args = []) {

        $method = (new \ReflectionClass($mgr))->getMethod('defineAction');
        $method->setAccessible(true);

        $method->invokeArgs($mgr, $args);

    }

    public function testComplexDefineAction () {

        $self = $this;
        $processFn = function (ActionContext $ctx) use (&$called, &$self) {

            $self->assertCount(3, $ctx->getParams());
            $called = true;

        };

        $mgr = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgr->method('getModuleName')->willReturn('ModuleName');

        $this->addUserAspect($mgr);
        $this->defineAction($mgr, ['create',
                                   ['param1' => [new RuntimeConstraintsProvider(
                                                     [
                                                         'param1' => [
                                                             new NotBlank(),
                                                             new Choice(['choices' => ['homme', 'femme']])
                                                         ]
                                                     ])
                                   ]
                                    ,
                                    'param2' => [new RuntimeConstraintsProvider(
                                                     [
                                                         'param2' => [
                                                             new NotBlank(),
                                                             new DateTime()
                                                         ]
                                                     ])
                                    ]

                                   ],
                                   $processFn
        ]);

        $actions = ApplicationContext::getInstance()->getActions();
        $this->assertCount(1, $actions);
        $action = $actions[0];
        $this->assertSame('create', $action->getName());
        $this->assertSame('ModuleName', $action->getModule());

        $actionContext = $this->getActionContextWithParams(['param0' => new UnsafeParameter('qwe', ''),
                                                            'param1' => new UnsafeParameter('homme', ''),
                                                            'param2' => new \DateTime()
                                                           ]);

        $action->process($actionContext);
        $this->assertTrue($called);

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidParameterDefineAction () {

        $mgr = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgr->method('getModuleName')->willReturn('ModuleName');

        $this->addUserAspect($mgr);
        $this->defineAction($mgr, ['create',
                                   ['qwe' => [new RuntimeConstraintsProvider(
                                                  [
                                                      'qwe' => [
                                                          new NotBlank()
                                                      ]
                                                  ])
                                   ]
                                   ],
                                   $this->getCallable()
        ]);

        $actionContext = $this->getActionContextWithParams(['aze' => new UnsafeParameter('qwe', '')]);

        ApplicationContext::getInstance()->getActions()[0]->process($actionContext);

    }

    /**
     * @expectedException \Exception
     */
    public function testDefineActionInvalidConstraint () {

        $mgr = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgr->method('getModuleName')->willReturn('ModuleName');

        $this->addUserAspect($mgr);
        $this->defineAction($mgr, ['create',
                                   ['param1' => [new RuntimeConstraintsProvider(
                                                     [
                                                         'param1' => [
                                                             new NotBlank(),
                                                             new Choice(['choices' => ['homme', 'femme']])
                                                         ]
                                                     ])
                                   ]
                                   ],
                                   $this->getCallable()
        ]);


        $actionContext = $this->getActionContextWithParams(['param1' => new UnsafeParameter('hommes', '')]);

        ApplicationContext::getInstance()->getActions()[0]->process($actionContext);

    }

    /**
     * @expectedException \Exception
     */
    public function testDefineActionInvalidConstraintOfModelAspect () {

        $appCtx = ApplicationContext::getInstance();

        $mgr = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgr->method('getModuleName')->willReturn('ModuleName');

        $this->setMainEntity($mgr, ['model' => 'TestUser',]);
        $this->addCompanyAspect($mgr);

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $userModuleManager->load($appCtx);
        $companyModuleManager->load($appCtx);

        $actionContext = $this->getMockActionContext();
        $actionContext->method('getParams')->willReturn(['company' => new UnsafeParameter('qwe', '')]);

        // TODO: improve test to check that the excepted exception which is thrown
        // in this case this is not the good one. should improve the test to add some values in ActCtx

        $mgr->magicalCreate($actionContext);

    }

    public function testSimpleMagicalCreate () {

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Core');

        $mgr = $this->getMockMagicalModuleManager(['getMagicalEntityObject']);
        $mgr->method('getMagicalEntityObject')->will($this->returnCallback(function () use (&$mgr) {

            return $this->getMainEntity($mgr);

        }));

        $userModuleManager = new UserModuleManager();

        $this->setMainEntity($mgr, [
            'model' => 'TestUser',
        ]);


        $appCtx = ApplicationContext::getInstance();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);

        $actionContext = $this->getActionContextWithParams(
            ['email'    => 'qwe@qwe1.com',
             'password' => new UnsafeParameter('qwe', '')
            ]);

        /**
         * @var User $user
         */
        $user = $this->magicalAction('Create', $mgr, [$actionContext]);
        $this->assertInstanceOf(Registry::realModelClassName('TestUser'), $user);
        $this->assertSame('qwe@qwe1.com', $user->getEmail());
        $this->assertSame(UserHelper::encryptPassword($user->getSalt(), 'qwe'), $user->getPassword());

    }

    /**
     * @param MagicalModuleManager $mgr
     *
     * @return mixed
     */
    protected function getMainEntity (MagicalModuleManager &$mgr) {

        $method = (new \ReflectionClass($mgr))->getMethod('getMainEntity');
        $method->setAccessible(true);

        return $method->invoke($mgr);
    }

    /**
     * @param                      $action
     * @param MagicalModuleManager $mgr
     * @param array                $args
     *
     * @return mixed
     */
    protected function magicalAction ($action, MagicalModuleManager &$mgr, array $args = []) {

        $method = (new \ReflectionClass($mgr))->getMethod('magical' . $action);
        $method->setAccessible(true);

        return $method->invokeArgs($mgr, $args);

    }

    public function testComplexMagicalCreate () {

        $mgr = $this->getMockMagicalModuleManager(['getMagicalEntityObject']);
        $mgr->method('getMagicalEntityObject')->will($this->returnCallback(function () use (&$mgr) {

            return $this->getMainEntity($mgr);

        }));

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();
        $this->setMainEntity($mgr, [
            'model' => 'TestUser',
        ]);
        $this->addCompanyAspect($mgr);

        $this->addAspect($mgr, [
            'model'   => 'TestStorage',
            'prefix'  => 's3',
            'keyPath' => 'company.storage',
            'create'  => [
                'constraints' => [new Object(), new NotBlank()],
            ]
        ]);


        $appCtx = ApplicationContext::getInstance();
        $appCtx->setProduct('Core');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);
        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);
        $storageModuleManager->loadActions($appCtx);
        $storageModuleManager->loadHelpers($appCtx);

        $actionContext = $this->getActionContextWithParams(
            [
                'email'    => 'qwe@qwe2.com',
                'name'     => 'thierry',
                'password' => new UnsafeParameter('qwe', ''),
                'firm'     => [
                    'name' => new UnsafeParameter('bigsool', '')
                ],
                's3'       => new UnsafeParameter(
                    ['url' => new UnsafeParameter('http://ddfd.fr', '')], ''),

            ]);

        /**
         * @var TestUser $user
         */
        $user = $this->magicalAction('Create', $mgr, [$actionContext]);


        $this->assertInstanceOf(Registry::realModelClassName('TestUser'), $user);
        $this->assertSame('qwe@qwe2.com', $user->getEmail());
        $this->assertSame(UserHelper::encryptPassword($user->getSalt(), 'qwe'), $user->getPassword());
        $this->assertSame('bigsool', $user->getUnrestrictedCompany()->getName());
        $this->assertContainsOnly($user, $user->getUnrestrictedCompany()->getUnrestrictedUsers());

    }

    public function testMagicalCreateWithTwoMagicalModuleManager () {

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();

        $mgrUser = $this->getMockMagicalModuleManager(['getModuleName', 'getMagicalEntityObject']);
        $mgrUser->method('getModuleName')->willReturn('UserModule');
        $mgrUser->method('getMagicalEntityObject')->will($this->returnCallback(function () use (&$mgrUser) {

            return $this->getMainEntity($mgrUser);

        }));
        $this->setMainEntity($mgrUser, [
            'model' => 'TestUser',
        ]);


        $this->addAspect($mgrUser, [
            'model'   => 'TestCompany',
            'prefix'  => 'firm',
            'keyPath' => 'company',
            'create'  => [
                'constraints' => [new Object(), new NotBlank()],
                'action'      => new ActionReference('Archipad\\Group', 'create'),
            ]
        ]);

        $mgrCompany = $this->getMockMagicalModuleManager(['getModuleName', 'getMagicalEntityObject']);
        $mgrCompany->method('getModuleName')->willReturn('Archipad\Group');
        $mgrCompany->method('getMagicalEntityObject')->will($this->returnCallback(function () use (&$mgrCompany) {

            return $this->getMainEntity($mgrCompany);

        }));
        $this->setMainEntity($mgrCompany, [
            'model' => 'TestCompany',
        ]);
        $this->addAspect($mgrCompany, [
            'model'   => 'TestStorage',
            'keyPath' => 'storage',
            'prefix'  => 's3',
        ]);
        $self = $this;
        $called = false;


        $appCtx = ApplicationContext::getInstance();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);
        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);
        $storageModuleManager->loadActions($appCtx);
        $storageModuleManager->loadHelpers($appCtx);

        $actionContext = $this->getActionContextWithParams(
            ['email'    => 'thomas@bigsool.com',
             'name'     => 'thomas',
             'password' => new UnsafeParameter('qwe', ''),
             'firm'     => new UnsafeParameter(
                 ['name' => new UnsafeParameter('bigsool', ''),
                  's3'   => new UnsafeParameter(
                      ['url' => new UnsafeParameter('http://www.bigsool.com', '')], ''),
                 ], ''),

            ]);

        $this->defineAction($mgrCompany, ['create',
                                          ['name' => [new RuntimeConstraintsProvider(
                                                          [
                                                              'name' => [new NotBlank()]
                                                          ]
                                                      )
                                          ]

                                          ],
                                          function (ActionContext $context) use (&$self, &$called, &$mgrCompany) {

                                              $params = $context->getParams();
                                              $self->assertCount(2, $params);
                                              $self->assertArrayHasKey('name', $params);
                                              $self->assertSame('bigsool', $params['name']);
                                              $self->assertArrayHasKey('s3', $params);
                                              $self->assertInstanceOf('\Core\Parameter\UnsafeParameter', $params['s3']);
                                              $storageParams = $params['s3']->getValue();
                                              $self->assertInternalType('array', $storageParams);
                                              $self->assertArrayHasKey('url', $storageParams);
                                              $self->assertInstanceOf('\Core\Parameter\UnsafeParameter',
                                                                      $storageParams['url']);
                                              $self->assertSame('http://www.bigsool.com',
                                                                $storageParams['url']->getValue());

                                              $called = true;
                                              $company = $self->magicalAction('Create', $mgrCompany, [$context]);

                                              return $company;

                                          }
        ]);

        /**
         * @var TestUser $user
         */
        $user = $this->magicalAction('Create', $mgrUser, [$actionContext]);

        $user->setOwnedCompany($user->getUnrestrictedCompany());
        $user->getUnrestrictedCompany()->setOwner($user);
        ApplicationContext::getInstance()->getNewRegistry()->save($user);

        $this->assertTrue($called);
        $this->assertInstanceOf(Registry::realModelClassName('TestUser'), $user);
        $this->assertSame('thomas@bigsool.com', $user->getEmail());
        $this->assertSame(UserHelper::encryptPassword($user->getSalt(), 'qwe'), $user->getPassword());
        $this->assertSame('bigsool', $user->getUnrestrictedCompany()->getName());
        $this->assertContainsOnly($user, $user->getUnrestrictedCompany()->getUnrestrictedUsers());

    }

    public function testSimpleMagicalUpdate () {

        $mgr = $this->getMockMagicalModuleManager(['getMagicalEntityObject']);
        $mgr->method('getMagicalEntityObject')->will($this->returnCallback(function () use (&$mgr) {

            return $this->getMainEntity($mgr);

        }));

        $userModuleManager = new UserModuleManager();

        $this->setMainEntity($mgr, [
            'model' => 'TestUser',
        ]);

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Core');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);

        $actionContext = $this->getActionContextWithParams(
            ['id'        => 1,
             'email'     => 'thierry@bigsool.com',
             'name'      => 'sambussy',
             'firstname' => 'thierry',
             'password'  => 'qweqwe',
            ]);

        /**
         * @var User $user
         */
        $user = $this->magicalAction('Update', $mgr, [$actionContext]);

        $this->assertInstanceOf(Registry::realModelClassName('TestUser'), $user);
        $this->assertSame('thierry@bigsool.com', $user->getEmail());
        $this->assertSame('sambussy', $user->getName());
        $this->assertSame('thierry', $user->getFirstname());
        $this->assertSame('qweqwe', $user->getPassword());
    }

    public function testComplexMagicalUpdate () {


        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();


        $mgrUser = $this->getMockMagicalModuleManager(['getModuleName', 'getMagicalEntityObject']);
        $mgrUser->method('getModuleName')->willReturn('UserModule');
        $mgrUser->method('getMagicalEntityObject')->will($this->returnCallback(function () use (&$mgrUser) {

            return $this->getMainEntity($mgrUser);

        }));

        $this->setMainEntity($mgrUser, [
            'model' => 'TestUser',
        ]);

        $this->addAspect($mgrUser, [
            'model'   => 'TestCompany',
            'prefix'  => 'firm',
            'keyPath' => 'company',
            'update'  => [
                'constraints' => [new Object(), new NotBlank()],
            ]
        ]);

        $this->addAspect($mgrUser, [
            'model'   => 'TestStorage',
            'prefix'  => 's3',
            'keyPath' => 'company.storage',
            'update'  => [
                'constraints' => [new Object(), new NotBlank()],
            ]
        ]);


        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);
        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);
        $storageModuleManager->loadActions($appCtx);
        $storageModuleManager->loadHelpers($appCtx);


        $actionContext = $this->getActionContextWithParams(
            ['id'        => 1,
             'email'     => 'julien@bigsool.com',
             'name'      => 'ferrier',
             'firstname' => 'julien',
             'password'  => 'bla',
             'firm'      => new UnsafeParameter(['name' => 'bigsoole'], ''),
             's3'        => ['url' => new UnsafeParameter('http://www.bigsoole.com', '')]

            ]);

        /*
         * @var TestUser $user
         */
        $user = $this->magicalAction('Update', $mgrUser, [$actionContext]);
        $this->assertInstanceOf(Registry::realModelClassName('TestUser'), $user);
        $this->assertSame('julien@bigsool.com', $user->getEmail());
        $this->assertSame('ferrier', $user->getName());
        $this->assertSame('julien', $user->getFirstname());
        $this->assertSame('bla', $user->getPassword());

        $this->assertSame('bigsoole', $user->getUnrestrictedCompany()->getName());


        $this->assertSame('http://www.bigsoole.com',
                          $user->getUnrestrictedCompany()->getUnrestrictedStorage()->getUrl());

    }

    public function testMagicalUpdateWithTwoMagicalModuleManager () {


        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();


        $mgrUser = $this->getMockMagicalModuleManager(['getModuleName', 'getMagicalEntityObject']);
        $mgrUser->method('getModuleName')->willReturn('UserModule');
        $mgrUser->method('getMagicalEntityObject')->will($this->returnCallback(function () use (&$mgrUser) {

            return $this->getMainEntity($mgrUser);

        }));

        $this->setMainEntity($mgrUser, [
            'model' => 'TestUser',
        ]);


        $this->addAspect($mgrUser, [
            'model'   => 'TestCompany',
            'prefix'  => 'firm',
            'keyPath' => 'company',
            'update'  => [
                'constraints' => [new Object(), new NotBlank()],
                'action'      => new ActionReference('Archipad\\Group', 'update'),
            ]
        ]);

        $mgrCompany = $this->getMockMagicalModuleManager(['getModuleName', 'getMagicalEntityObject']);
        $mgrCompany->method('getModuleName')->willReturn('Archipad\Group');
        $mgrCompany->method('getMagicalEntityObject')->will($this->returnCallback(function () use (&$mgrCompany) {

            return $this->getMainEntity($mgrCompany);

        }));
        $this->setMainEntity($mgrCompany, [
            'model' => 'TestCompany',
        ]);

        $this->addAspect($mgrCompany, [
            'model'   => 'TestStorage',
            'keyPath' => 'storage',
            'prefix'  => 's3',
        ]);


        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);
        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);
        $storageModuleManager->loadActions($appCtx);
        $storageModuleManager->loadHelpers($appCtx);

        $self = $this;
        $called = false;


        $actionContext = $this->getActionContextWithParams(
            ['id'        => 1,
             'email'     => 'laurent@bigsool.com',
             'name'      => 'wozniak',
             'firstname' => 'laurent',
             'password'  => 'bli',
             'firm'      => new UnsafeParameter(['name' => 'bigsoolee',
                                                 's3'   => ['url' => 'http://www.bigsoolee.com']
                                                ], '')
            ]);

        $this->defineAction($mgrCompany, ['update',
                                          ['name' => [new RuntimeConstraintsProvider(['name' => [new NotBlank()]])]],
                                          function (ActionContext $context) use (&$self, &$called, &$mgrCompany) {

                                              $params = $context->getParams();
                                              // TODO: check parameter id
                                              $self->assertCount(3, $params);
                                              $self->assertArrayHasKey('name', $params);
                                              $self->assertSame('bigsoolee', $params['name']);
                                              $self->assertArrayHasKey('s3', $params);
                                              $storageParams = $params['s3'];
                                              $self->assertInternalType('array', $storageParams);
                                              $self->assertArrayHasKey('url', $storageParams);
                                              $self->assertSame('http://www.bigsoolee.com', $storageParams['url']);
                                              $called = true;

                                              return $self->magicalAction('Update', $mgrCompany, [$context]);

                                          }
        ]);
        /*
         * @var TestUser $user
         */
        $user = $this->magicalAction('Update', $mgrUser, [$actionContext]);
        $this->assertInstanceOf(Registry::realModelClassName('TestUser'), $user);
        $this->assertSame('laurent@bigsool.com', $user->getEmail());
        $this->assertSame('wozniak', $user->getName());
        $this->assertSame('laurent', $user->getFirstname());
        $this->assertSame('bli', $user->getPassword());

        $this->assertSame('bigsoolee', $user->getCompany()->getName());


        $this->assertSame('http://www.bigsoolee.com', $user->getCompany()->getStorage()->getUrl());

    }

    public function testMagicalFindObject () {

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();

        $mgrUser = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgrUser->method('getModuleName')->willReturn('TestAccount');


        $this->setMainEntity($mgrUser, [
            'model' => 'TestUser',
        ]);

        $this->addAspect($mgrUser, [
            'model'   => 'TestCompany',
            'keyPath' => 'company',
            'prefix'  => 'firm',
        ]);

        $this->addAspect($mgrUser, [
            'model'   => 'TestStorage',
            'keyPath' => 'company.storage',
            'prefix'  => 's3',
        ]);

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);
        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);
        $storageModuleManager->loadActions($appCtx);
        $storageModuleManager->loadHelpers($appCtx);


        $filters =
            [new StringFilter('TestUser', 'bla', 'id = 1')];
        $values = ['user.*', 'company.*', 'storage.*'];
        $alias = []; //[ 'company.name' => 'companyName'];

        $result = $this->magicalAction('Find', $mgrUser, [new RequestContext(), $values, $filters]);
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) == 1);
        /**
         * @var TestAccount $account
         */
        $account = $result[0];
        $this->assertInstanceOf('\Core\Model\TestAccount', $account);
        $this->assertInstanceOf('\Core\Model\TestCompany', $account->getUnrestrictedCompany());
        $this->assertEquals('u1@bigsool.com', $account->getUser()->getEmail());
        $this->assertEquals('Bigsool', $account->getUnrestrictedCompany()->getName());
        $this->assertEquals('http://www.amazon.com/', $account->getUnrestrictedCompanyStorage()->getUrl());

    }

    public function testMagicalFindArray () {

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();

        $mgrUser = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgrUser->method('getModuleName')->willReturn('TestAccount');

        $this->setMainEntity($mgrUser, [
            'model' => 'TestUser',
        ]);

        $this->addAspect($mgrUser, [
            'model'   => 'TestCompany',
            'keyPath' => 'company',
            'prefix'  => 'firm',
        ]);

        $this->addAspect($mgrUser, [
            'model'   => 'TestStorage',
            'keyPath' => 'company.storage',
            'prefix'  => 's3',
        ]);

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);
        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);
        $storageModuleManager->loadActions($appCtx);
        $storageModuleManager->loadHelpers($appCtx);


        $filters =
            [new StringFilter('TestUser', 'bla', 'id = 1')];
        $values = ['user.*', 'firm.*', 's3.*'];

        $result = $this->magicalAction('Find', $mgrUser, [new RequestContext(), $values, $filters, [], true]);
        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) == 1);
        $result = $result[0];
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('firm', $result);
        $this->assertInternalType('array', $result['firm']);
        $this->assertArrayHasKey('name', $result['firm']);
        $this->assertEquals('Bigsool', $result['firm']['name']);
        $this->assertArrayHasKey('s3', $result);
        $this->assertInternalType('array', $result['s3']);
        $this->assertArrayHasKey('url', $result['s3']);
        $this->assertEquals('http://www.amazon.com/', $result['s3']['url']);

    }

    /**
     * @depends testMagicalCreateWithTwoMagicalModuleManager
     */
    public function testMagicalDeleteOneToOne () {

        $filters = [new StringFilter('TestUser', 'bla', 'id = 2')];

        $mgrUser = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgrUser->method('getModuleName')->willReturn('TestAccount');

        $this->setMainEntity($mgrUser, [
            'model' => 'TestUser',
        ]);

        $this->magicalAction('Delete', $mgrUser, [$filters]);

        $userModuleManager = new UserModuleManager();

        $mgrUser = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgrUser->method('getModuleName')->willReturn('TestAccount');

        $this->setMainEntity($mgrUser, [
            'model' => 'TestUser',
        ]);

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);

        $result = $this->magicalAction('Find', $mgrUser, [new RequestContext(), ['user.*'], $filters, [], true]);

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) == 0);

        $companyModuleManager = new CompanyModuleManager();
        $mgrCompany = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgrCompany->method('getModuleName')->willReturn('TestAccount');

        $this->setMainEntity($mgrCompany, [
            'model' => 'TestCompany',
        ]);

        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);

        $filters = [new StringFilter('TestCompany', 'bla', 'id = 1')];

        $result = $this->magicalAction('Find', $mgrCompany, [new RequestContext(), ['company.*'], [], [], true]);

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) == 0);

    }

    public function testMagicalDeleteOneToMany () {

        $filters = [new StringFilter('TestUser', 'bla', 'id = 3')];

        $mgrUser = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgrUser->method('getModuleName')->willReturn('TestAccount');

        $this->setMainEntity($mgrUser, [
            'model' => 'TestUser',
        ]);

        $this->magicalAction('Delete', $mgrUser, [$filters]);

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Archipad');

        $userModuleManager = new UserModuleManager();
        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);

        $result = $this->magicalAction('Find', $mgrUser, [new RequestContext(), ['user.*'], $filters, [], true]);

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) == 0);

        $companyModuleManager = new CompanyModuleManager();
        $mgrCompany = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgrCompany->method('getModuleName')->willReturn('TestAccount');

        $this->setMainEntity($mgrCompany, [
            'model' => 'TestCompany',
        ]);

        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);

        $filters = [new StringFilter('TestCompany', 'bla', 'id = 1')];

        $result =
            $this->magicalAction('Find', $mgrCompany, [new RequestContext(), ['company.*'], $filters, [], true]);

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) == 1);
        $this->assertEquals('Bigsool', $result[0]['name']);
    }

    public function setUp () {

        parent::setUp();

        self::resetApplicationContext();

        $ctx = $this->getApplicationContext(self::$doctrineConnectionSettings);

        $prop = new \ReflectionProperty($ctx, 'entityManager');
        $prop->setAccessible(true);
        /**
         * @var EntityManager $em
         */
        $em = $prop->getValue($ctx);
        $em->beginTransaction();
    }

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        $ctx = self::getApplicationContext();


        $prop = new \ReflectionProperty($ctx, 'entityManager');
        $prop->setAccessible(true);

        $em = $prop->getValue($ctx);

        self::$doctrineConnectionSettings = $em->getConnection();
        self::resetDatabase($ctx);

        self::$company1 = new TestCompany();
        self::$company1->setName('Bigsool');
        self::$user1 = new TestUser();
        self::$user1->setEmail('u1@bigsool.com');
        self::$user1->setPassword('qwe');
        self::$user1->setCompany(self::$company1);
        self::$user1->setRegisterDate(new \DateTime());
        self::$user2 = new TestUser();
        self::$user2->setEmail('u2@bigsool.com');
        self::$user2->setCompany(self::$company1);
        self::$user2->setPassword('qwe');
        self::$user2->setRegisterDate(new \DateTime());
        self::$user3 = new TestUser();
        self::$user3->setEmail('u3@bigsool.com');
        self::$user3->setCompany(self::$company1);
        self::$user3->setPassword('qwe');
        self::$user3->setRegisterDate(new \DateTime());
        self::$company1->setOwner(self::$user3);

        self::$storage = new TestStorage();
        self::$storage->setUrl('http://www.amazon.com/');
        self::$storage->setCompany(self::$company1);
        self::$storage->setLogin('login');
        self::$storage->setPassword('qwe');
        self::$storage->setUsedspace(0);
        self::$storage->setLastusedspaceupdate(new \DateTime());
        self::$storage->setIsoutofquota(false);
        self::$company1->setStorage(self::$storage);
        self::$company1->addUser(self::$user1);
        self::$company1->addUser(self::$user2);
        self::$company1->addUser(self::$user3);

        $registry = self::getApplicationContext()->getNewRegistry();
        $registry->save(self::$user1);
        $registry->save(self::$user2);
        $registry->save(self::$user3);
        $registry->save(self::$company1);
        $registry->save(self::$storage);


        self::$user1->setRegisterDate((new \DateTime())->getTimestamp());
        self::$user2->setRegisterDate((new \DateTime())->getTimestamp());
        self::$user3->setRegisterDate((new \DateTime())->getTimestamp());

        self::$storage->setLastUsedSpaceUpdate((new \DateTime())->getTimestamp());

    }

    protected function tearDown () {

        $whiteList =
            [];
        $currentTestFcName = $this->getName();
        if (!in_array($currentTestFcName, $whiteList)) {
            $this->rollBackDatabase();
        }
        else {
            $this->commitDB();
        }

    }

    protected function rollBackDatabase () {

        $appCtx = ApplicationContext::getInstance();
        $prop = new \ReflectionProperty($appCtx, 'entityManager');
        $prop->setAccessible(true);

        /**
         * @var EntityManager $em
         */
        $em = $prop->getValue($appCtx);
        if (isset($em)) {
            $em->rollback();
        }

    }

    protected function commitDB () {

        $appCtx = ApplicationContext::getInstance();
        $prop = new \ReflectionProperty($appCtx, 'entityManager');
        $prop->setAccessible(true);
        /**
         * @var EntityManager $em
         */
        $em = $prop->getValue($appCtx);
        $em->commit();

    }

    public function testMagicalWithTwoStorage () {

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();

        $mgrUser = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgrUser->method('getModuleName')->willReturn('TestAccount');

        $this->setMainEntity($mgrUser, [
            'model' => 'TestUser',
        ]);

        $this->addAspect($mgrUser, [
            'model'   => 'TestCompany',
            'keyPath' => 'company',
            'prefix'  => 'firm',
        ]);

        $this->addAspect($mgrUser, [
            'model'   => 'TestStorage',
            'keyPath' => 'storage',
            'prefix'  => 's3',
        ]);

        $this->addAspect($mgrUser, [
            'model'   => 'TestStorage',
            'keyPath' => 'company.storage',
            'prefix'  => 'firm.s3',
        ]);

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);
        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);
        $storageModuleManager->loadActions($appCtx);
        $storageModuleManager->loadHelpers($appCtx);

        $actionContext = $this->getActionContextWithParams(
            [
                'name'     => 'name',
                'email'    => 'e@ma.il',
                'password' => 'password',
                'firm'     => [
                    'name' => 'firm name',
                    's3'   => [
                        'url' => 'firm storage url'
                    ]
                ],
                's3'       => [
                    'login' => 'storage login',
                    'url'   => 'storage url',
                ]

            ]);

        /**
         * @var TestAccount $account
         * @var TestUser    $user
         */
        $account = $this->magicalAction('Create', $mgrUser, [$actionContext]);


        $filters = [new StringFilter('TestUser', 'bla', 'id = :id')];
        $values =
            ['name',
             'firm.name',
             'firm.s3.url',
             's3.login'
             /*, 's3.url' => requested field (present in RequestContext) */
            ];

        $requestCtx = new RequestContext();
        $requestCtx->setReturnedFields([new RelativeField('s3.url')]);

        $result =
            $this->magicalAction('Find', $mgrUser, [$requestCtx,
                                                    $values,
                                                    $filters,
                                                    ['id' => $account->getUser()->getId()],
                                                    true
            ]);
        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) == 1);
        $result = $result[0];
        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals('name', $result['name']);
        $this->assertArrayHasKey('firm', $result);

        $this->assertInternalType('array', $result['firm']);
        $this->assertArrayHasKey('name', $result['firm']);
        $this->assertEquals('firm name', $result['firm']['name']);

        $this->assertArrayHasKey('s3', $result['firm']);
        $this->assertInternalType('array', $result['firm']['s3']);
        $this->assertEquals('firm storage url', $result['firm']['s3']['url']);

        $this->assertArrayHasKey('s3', $result);
        $this->assertInternalType('array', $result['s3']);
        $this->assertArrayHasKey('url', $result['s3']);
        $this->assertEquals('storage url', $result['s3']['url']);

    }

    public function testDisabledKeyPathsMagicalCreate () {

        $mgr = $this->getMockMagicalModuleManager();

        $userCreateCalled = false;
        $companyCreateCalled = false;
        $storageCreateCalled = false;
        $companyStorageCreateCalled = false;

        $this->setMainEntity($mgr, [
            'model'  => 'TestUser',
            'create' => ['action' => new SimpleAction('test', 'test', [], [], function () use (&$userCreateCalled) {

                $userCreateCalled = true;

            })
            ]
        ]);

        $this->addAspect($mgr, [
            'model'   => 'TestCompany',
            'keyPath' => 'company',
            'prefix'  => 'firm',
            'create'  => ['action' => new SimpleAction('test', 'test', [], [], function () use (&$companyCreateCalled) {

                $companyCreateCalled = true;

            })
            ]
        ]);

        $this->addAspect($mgr, [
            'model'   => 'TestStorage',
            'keyPath' => 'storage',
            'prefix'  => 's3',
            'create'  => ['action' => new SimpleAction('test', 'test', [], [], function () use (&$storageCreateCalled) {

                $storageCreateCalled = true;

            })
            ]
        ]);

        $this->addAspect($mgr, [
            'model'   => 'TestStorage',
            'keyPath' => 'company.storage',
            'prefix'  => 'firm.s3',
            'create'  => ['action' => new SimpleAction('test', 'test', [], [],
                function () use (&$companyStorageCreateCalled) {

                    $companyStorageCreateCalled = true;

                })
            ]
        ]);

        $actionContext = $this->getActionContextWithParams(
            [
                'name'     => 'name',
                'email'    => 'e@ma.il',
                'password' => 'password',
                'firm'     => [
                    'name' => 'firm name',
                    's3'   => [
                        'url' => 'firm storage url'
                    ]
                ],
                's3'       => [
                    'login' => 'storage login',
                    'url'   => 'storage url',
                ]

            ]);

        $this->magicalAction('Create', $mgr, [$actionContext, ['company']]);

        $this->assertTrue($userCreateCalled);
        $this->assertTrue($storageCreateCalled);
        $this->assertFalse($companyCreateCalled);
        $this->assertFalse($companyStorageCreateCalled);

        $userCreateCalled = false;
        $companyCreateCalled = false;
        $storageCreateCalled = false;
        $companyStorageCreateCalled = false;

        $this->magicalAction('Create', $mgr, [$actionContext]);

        $this->assertTrue($userCreateCalled);
        $this->assertTrue($storageCreateCalled);
        $this->assertTrue($companyCreateCalled);
        $this->assertTrue($companyStorageCreateCalled);

    }

    public function testDisabledKeyPathsMagicalUpdate () {

        $mgr = $this->getMockMagicalModuleManager();

        $userUpdateCalled = false;
        $companyUpdateCalled = false;
        $storageUpdateCalled = false;
        $companyStorageUpdateCalled = false;

        $this->setMainEntity($mgr, [
            'model'  => 'TestUser',
            'update' => ['action' => new SimpleAction('test', 'test', [], [], function () use (&$userUpdateCalled) {

                $userUpdateCalled = true;

            })
            ]
        ]);

        $this->addAspect($mgr, [
            'model'   => 'TestCompany',
            'keyPath' => 'company',
            'prefix'  => 'firm',
            'update'  => ['action' => new SimpleAction('test', 'test', [], [], function () use (&$companyUpdateCalled) {

                $companyUpdateCalled = true;

            })
            ]
        ]);

        $this->addAspect($mgr, [
            'model'   => 'TestStorage',
            'keyPath' => 'storage',
            'prefix'  => 's3',
            'update'  => ['action' => new SimpleAction('test', 'test', [], [], function () use (&$storageUpdateCalled) {

                $storageUpdateCalled = true;

            })
            ]
        ]);

        $this->addAspect($mgr, [
            'model'   => 'TestStorage',
            'keyPath' => 'company.storage',
            'prefix'  => 'firm.s3',
            'update'  => ['action' => new SimpleAction('test', 'test', [], [],
                function () use (&$companyStorageUpdateCalled) {

                    $companyStorageUpdateCalled = true;

                })
            ]
        ]);

        $actionContext = $this->getActionContextWithParams(
            [
                'name'     => 'name',
                'email'    => 'e@ma.il',
                'password' => 'password',
                'firm'     => [
                    'name' => 'firm name',
                    's3'   => [
                        'url' => 'firm storage url'
                    ]
                ],
                's3'       => [
                    'login' => 'storage login',
                    'url'   => 'storage url',
                ]

            ]);

        $this->magicalAction('Update', $mgr, [$actionContext, ['company']]);

        $this->assertTrue($userUpdateCalled);
        $this->assertTrue($storageUpdateCalled);
        $this->assertFalse($companyUpdateCalled);
        $this->assertFalse($companyStorageUpdateCalled);

    }

    public function testDisabledKeyPathsMagicalFind () {

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();

        $mgrUser = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgrUser->method('getModuleName')->willReturn('TestAccount');

        $this->setMainEntity($mgrUser, [
            'model' => 'TestUser',
        ]);

        $this->addAspect($mgrUser, [
            'model'   => 'TestCompany',
            'keyPath' => 'company',
            'prefix'  => 'firm',
        ]);

        $this->addAspect($mgrUser, [
            'model'   => 'TestStorage',
            'keyPath' => 'storage',
            'prefix'  => 's3',
        ]);

        $this->addAspect($mgrUser, [
            'model'   => 'TestStorage',
            'keyPath' => 'company.storage',
            'prefix'  => 'firm.s3',
        ]);

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);
        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);
        $storageModuleManager->loadActions($appCtx);
        $storageModuleManager->loadHelpers($appCtx);

        $actionContext = $this->getActionContextWithParams(
            [
                'name'     => 'name',
                'email'    => 'e@ma.il',
                'password' => 'password',
                'firm'     => [
                    'name' => 'firm name',
                    's3'   => [
                        'url' => 'firm storage url'
                    ]
                ],
                's3'       => [
                    'login' => 'storage login',
                    'url'   => 'storage url',
                ]

            ]);

        /**
         * @var TestAccount $account
         * @var TestUser    $user
         */
        $account = $this->magicalAction('Create', $mgrUser, [$actionContext]);


        $filters = [new StringFilter('TestUser', 'bla', 'id = :id')];
        $values =
            ['name',
             'firm.name',
             'firm.s3.url',
             's3.login'
             /*, 's3.url' => requested field (present in RequestContext) */
            ];

        $requestCtx = new RequestContext();
        $requestCtx->setReturnedFields([new RelativeField('s3.url')]);

        $result =
            $this->magicalAction('Find', $mgrUser, [$requestCtx,
                                                    $values,
                                                    $filters,
                                                    ['id' => $account->getUser()->getId()],
                                                    true,
                                                    ['company']
            ]);

        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) == 1);
        $result = $result[0];

        $this->assertInternalType('array', $result);
        $this->assertArrayHasKey('name', $result);
        $this->assertEquals('name', $result['name']);
        $this->assertArrayNotHasKey('firm', $result);

        $this->assertArrayHasKey('s3', $result);
        $this->assertInternalType('array', $result['s3']);
        $this->assertArrayHasKey('url', $result['s3']);
        $this->assertEquals('storage url', $result['s3']['url']);

    }

    public function testsMagicalCreateWithPrefixedFields () {

        $mgr = $this->getMockMagicalModuleManager(['getMagicalEntityObject']);
        $mgr->method('getMagicalEntityObject')->will($this->returnCallback(function () use (&$mgr) {

            return $this->getMainEntity($mgr);

        }));

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();
        $this->setMainEntity($mgr, [
            'model' => 'TestUser',
        ]);
        $this->addCompanyAspect($mgr);

        $this->addAspect($mgr, [
            'model'   => 'TestStorage',
            'prefix'  => 'firm.s3',
            'keyPath' => 'company.storage',
            'create'  => [
                'constraints' => [new Object(), new NotBlank()],
            ]
        ]);


        $appCtx = ApplicationContext::getInstance();
        $appCtx->setProduct('Core');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);
        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);
        $storageModuleManager->loadActions($appCtx);
        $storageModuleManager->loadHelpers($appCtx);

        $actionContext = $this->getActionContextWithParams(
            [
                'email'             => 'qwe@qwe2.com',
                'name'              => 'thierry',
                'password'          => new UnsafeParameter('qwe', ''),
                'firm_name'         => 'bigsoolee',
                'firm_s3_url'       => 'http://storage.fr',
                'firm_s3_usedSpace' => '123',


            ]);

        /**
         * @var TestUser $user
         */
        $user = $this->magicalAction('Create', $mgr, [$actionContext]);

        $this->assertInstanceOf(Registry::realModelClassName('TestUser'), $user);
        $this->assertSame('qwe@qwe2.com', $user->getEmail());
        $this->assertSame(UserHelper::encryptPassword($user->getSalt(), 'qwe'), $user->getPassword());
        $this->assertSame('bigsoolee', $user->getUnrestrictedCompany()->getName());
        $this->assertSame('http://storage.fr', $user->getUnrestrictedCompany()->getUnrestrictedStorage()->getUrl());
        $this->assertSame('123', $user->getUnrestrictedCompany()->getUnrestrictedStorage()->getUsedSpace());
        $this->assertContainsOnly($user, $user->getUnrestrictedCompany()->getUsers());

    }

    private function getMagicalUser ($firmWithPrefixedFields, $s3WithPrefixedFields) {

        $mgrUser = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgrUser->method('getModuleName')->willReturn('TestAccount');


        $this->setMainEntity($mgrUser, [
            'model' => 'TestUser',
        ]);

        $this->addAspect($mgrUser, [
            'model'              => 'TestCompany',
            'keyPath'            => 'company',
            'prefix'             => 'firm',
            'withPrefixedFields' => $firmWithPrefixedFields
        ]);

        $this->addAspect($mgrUser, [
            'model'              => 'TestStorage',
            'keyPath'            => 'company.storage',
            'prefix'             => 'firm.s3',
            'withPrefixedFields' => $s3WithPrefixedFields
        ]);

        return $mgrUser;

    }

    public function testMagicalFindWithPrefixedFields () {

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();

        $mgrUser = $this->getMagicalUser(true, true);

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);
        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);
        $storageModuleManager->loadActions($appCtx);
        $storageModuleManager->loadHelpers($appCtx);


        $filters =
            [new StringFilter('TestUser', 'bla', 'id = 1')];
        $values = ['email', 'firm_name', 'firm_s3_url'];

        $result = $this->magicalAction('Find', $mgrUser, [new RequestContext(), $values, $filters, [], true]);

        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) == 1);
        $result = $result[0];
        $this->assertInternalType('array', $result);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('u1@bigsool.com', $result['email']);
        $this->assertEquals(1, $result['firm_id']);
        $this->assertEquals('Bigsool', $result['firm_name']);
        $this->assertEquals(1, $result['firm_s3_id']);
        $this->assertEquals('http://www.amazon.com/', $result['firm_s3_url']);

        $mgrUser = $this->getMagicalUser(true, false);

        $result = $this->magicalAction('Find', $mgrUser, [new RequestContext(), $values, $filters, [], true]);

        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) == 1);
        $result = $result[0];
        $this->assertInternalType('array', $result);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('u1@bigsool.com', $result['email']);
        $this->assertEquals(1, $result['firm_id']);
        $this->assertEquals('Bigsool', $result['firm_name']);
        $this->assertInternalType('array', $result['firm_s3']);
        $this->assertEquals(1, $result['firm_s3']['id']);
        $this->assertEquals('http://www.amazon.com/', $result['firm_s3']['url']);


        $mgrUser = $this->getMagicalUser(false, true);

        $result = $this->magicalAction('Find', $mgrUser, [new RequestContext(), $values, $filters, [], true]);

        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) == 1);
        $result = $result[0];
        $this->assertInternalType('array', $result);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('u1@bigsool.com', $result['email']);
        $this->assertInternalType('array', $result['firm']);
        $this->assertEquals(1, $result['firm']['id']);
        $this->assertEquals('Bigsool', $result['firm']['name']);
        $this->assertEquals(1, $result['firm']['s3_id']);
        $this->assertEquals('http://www.amazon.com/', $result['firm']['s3_url']);

        $mgrUser = $this->getMagicalUser(false, false);

        $result = $this->magicalAction('Find', $mgrUser, [new RequestContext(), $values, $filters, [], true]);

        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) == 1);
        $result = $result[0];
        $this->assertInternalType('array', $result);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('u1@bigsool.com', $result['email']);
        $this->assertInternalType('array', $result['firm']);
        $this->assertEquals(1, $result['firm']['id']);
        $this->assertEquals('Bigsool', $result['firm']['name']);
        $this->assertInternalType('array', $result['firm']['s3']);
        $this->assertEquals(1, $result['firm']['s3']['id']);
        $this->assertEquals('http://www.amazon.com/', $result['firm']['s3']['url']);

        $mgrUser = $this->getMagicalUser(true, true);

        $result = $this->magicalAction('Find', $mgrUser, [new RequestContext(), $values, $filters, [], true]);

        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) == 1);
        $result = $result[0];
        $this->assertInternalType('array', $result);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('u1@bigsool.com', $result['email']);
        $this->assertEquals(1, $result['firm_id']);
        $this->assertEquals('Bigsool', $result['firm_name']);
        $this->assertEquals(1, $result['firm_s3_id']);
        $this->assertEquals('http://www.amazon.com/', $result['firm_s3_url']);

    }

    public function testMagicalFindWithReturnedPrefixedFields () {

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();

        $mgrUser = $this->getMagicalUser(true, false);

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);
        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);
        $storageModuleManager->loadActions($appCtx);
        $storageModuleManager->loadHelpers($appCtx);


        $filters =
            [new StringFilter('TestUser', 'bla', 'id = 1')];
        $values = ['email'];

        $requestCtx = new RequestContext();
        $requestCtx->setReturnedFields([new RelativeField('email'), new RelativeField('firm_name')]);

        $result = $this->magicalAction('Find', $mgrUser, [$requestCtx, $values, $filters, [], true]);

        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) == 1);
        $result = $result[0];
        $this->assertInternalType('array', $result);

        $this->assertEquals(1, $result['id']);
        $this->assertEquals('u1@bigsool.com', $result['email']);
        $this->assertEquals(1, $result['firm_id']);
        $this->assertEquals('Bigsool', $result['firm_name']);

    }

    public function testMagicalFindWithPrefixedFieldsAndManyRows () {

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();

        $mgrUser = $this->getMagicalUser(true, false);

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);
        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);
        $storageModuleManager->loadActions($appCtx);
        $storageModuleManager->loadHelpers($appCtx);

        $values = ['email'];

        $requestCtx = new RequestContext();
        $requestCtx->setReturnedFields([new RelativeField('firm_name'), new RelativeField('firm_s3_url')]);

        $result = $this->magicalAction('Find', $mgrUser, [$requestCtx, $values, [], [], true]);

        $this->assertInternalType('array', $result);
        $this->assertTrue(count($result) == 3);
        foreach ($result as $elem) {
            $this->assertInternalType('array', $elem);
            $this->assertEquals('Bigsool', $elem['firm_name']);
        }

    }

}
