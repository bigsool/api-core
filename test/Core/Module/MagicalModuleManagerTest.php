<?php


namespace Core\Module;


use Core\Action\ActionReference;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\RequestContext;
use Core\Filter\StringFilter;
use Core\Model\TestCompany;
use Core\Model\TestStorage;
use Core\Model\TestUser;
use Core\Model\User;
use Core\Module\Test\Company\ModuleManager as CompanyModuleManager;
use Core\Module\Test\Storage\ModuleManager as StorageModuleManager;
use Core\Module\Test\User\Helper as UserHelper;
use Core\Module\Test\User\ModuleManager as UserModuleManager;
use Core\Parameter\UnsafeParameter;
use Core\Registry;
use Core\TestCase;
use Core\Validation\Constraints\Dictionary;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Null;

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
        $this->assertNull($modelAspect->getKeyPath());

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
        $this->assertNull($modelAspect->getKeyPath());

        $modelAspect = $modelAspects[1];
        $this->assertSame('company', $modelAspect->getPrefix());
        $this->assertSame('TestCompany', $modelAspect->getModel());
        $keyPath = $modelAspect->getKeyPath();
        $this->assertInstanceOf('\Core\Expression\AbstractKeyPath', $keyPath);
        $this->assertSame('company', $keyPath->getValue());
        $validators = $modelAspect->getConstraints('create');
        $this->assertInternalType('array', $validators);
        $this->assertContainsOnlyInstancesOf('\Symfony\Component\Validator\Constraint', $validators);
        $this->assertCount(2, $validators);
        $this->assertInstanceOf('\Core\Validation\Constraints\Dictionary', $validators[0]);
        $this->assertInstanceOf('\Symfony\Component\Validator\Constraints\NotBlank', $validators[1]);

        $modelAspect = $modelAspects[2];
        $this->assertSame('storage', $modelAspect->getPrefix());
        $this->assertSame('TestStorage', $modelAspect->getModel());
        $keyPath = $modelAspect->getKeyPath();
        $this->assertInstanceOf('\Core\Expression\AbstractKeyPath', $keyPath);
        $this->assertSame('company.storage', $keyPath->getValue());
        $validators = $modelAspect->getConstraints('create');
        $this->assertInternalType('array', $validators);
        $this->assertContainsOnlyInstancesOf('\Symfony\Component\Validator\Constraint', $validators);
        $this->assertCount(1, $validators);
        $this->assertInstanceOf('\Symfony\Component\Validator\Constraints\Null', $validators[0]);

    }

    /**
     * @param MagicalModuleManager $mgr
     */
    protected function addCompanyAspect (MagicalModuleManager &$mgr) {

        $this->addAspect($mgr, [
            'model'   => 'TestCompany',
            'prefix'  => 'company',
            'keyPath' => 'company',
            'create'  => [
                'constraints' => [new Dictionary(), new NotBlank()],
            ],
            'update'  => [
                'constraints' => [new Dictionary(), new NotBlank()],
            ]
        ]);

    }

    /**
     * @param MagicalModuleManager $mgr
     */
    protected function addStorageAspect (MagicalModuleManager &$mgr) {

        $this->addAspect($mgr, [
            'model'   => 'TestStorage',
            'prefix'  => 'storage',
            'keyPath' => 'company.storage',
            'create'  => [
                'constraints' => [new Null()],
                'action'      => NULL,
            ]
        ]);

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
                'name'  => new UnsafeParameter('qwe SA'),
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
                                               ])
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
                                   ['param1' => [
                                       ERR_INVALID_NAME,
                                       [new NotBlank(), new Choice(['choices' => ['homme', 'femme']])]
                                   ],
                                    'param2' => [
                                        ERR_INVALID_PARAM_EMAIL,
                                        [new NotBlank(), new DateTime()]
                                    ]
                                   ],
                                   $processFn
        ]);

        $actions = ApplicationContext::getInstance()->getActions();
        $this->assertCount(1, $actions);
        $action = $actions[0];
        $this->assertSame('create', $action->getName());
        $this->assertSame('ModuleName', $action->getModule());

        $actionContext = $this->getActionContextWithParams(['param0' => new UnsafeParameter('qwe'),
                                                            'param1' => new UnsafeParameter('homme'),
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
        $this->defineAction($mgr, ['create', ['qwe' => [ERR_INVALID_NAME, [new NotBlank()]]], $this->getCallable()]);

        $actionContext = $this->getActionContextWithParams(['aze' => new UnsafeParameter('qwe')]);

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
                                   ['param1' => [
                                       ERR_INVALID_NAME,
                                       [new NotBlank(), new Choice(['choices' => ['homme', 'femme']])]
                                   ]
                                   ],
                                   $this->getCallable()
        ]);

        $actionContext = $this->getActionContextWithParams(['params1' => new UnsafeParameter('qwe')]);

        ApplicationContext::getInstance()->getActions()[0]->process($actionContext);

    }

    /**
     * @expectedException \Exception
     */
    public function testDefineActionInvalidConstraintOfModelAspect () {

        $mgr = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgr->method('getModuleName')->willReturn('ModuleName');

        $this->addUserAspect($mgr);
        $this->addCompanyAspect($mgr);
        $this->defineAction($mgr, ['create', [], $this->getCallable()]);

        $actionContext = $this->getMockActionContext();
        $actionContext->method('getParams')->willReturn(['company' => new UnsafeParameter('qwe')]);

        ApplicationContext::getInstance()->getActions()[0]->process($actionContext);

    }

    public function testSimpleMagicalCreate () {

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
             'password' => new UnsafeParameter('qwe')
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
     * @param MagicalModuleManager $mgr
     * @param array                $params
     */
    protected function setMainEntity (MagicalModuleManager &$mgr, array $params) {

        $method = (new \ReflectionClass($mgr))->getMethod('setMainEntity');
        $method->setAccessible(true);

        $method->invokeArgs($mgr, [$params]);

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
            'prefix'  => 'storage',
            'keyPath' => 'company.storage',
            'create'  => [
                'constraints' => [new Null()],
            ]
        ]);


        $appCtx = ApplicationContext::getInstance();
        $appCtx->setProduct('Archipad');

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
                'password' => new UnsafeParameter('qwe'),
                'company'  => [
                    'name' => new UnsafeParameter('bigsool')
                ],
                'storage'  => new UnsafeParameter(
                    ['url' => new UnsafeParameter('http://ddfd.fr')]),

            ]);

        /**
         * @var User $user
         */
        $user = $this->magicalAction('Create', $mgr, [$actionContext]);


        $this->assertInstanceOf(Registry::realModelClassName('TestUser'), $user);
        $this->assertSame('qwe@qwe2.com', $user->getEmail());
        $this->assertSame(UserHelper::encryptPassword($user->getSalt(), 'qwe'), $user->getPassword());
        $this->assertSame('bigsool', $user->getCompany()->getName());
        $this->assertContainsOnly($user, $user->getCompany()->getUsers());

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
            'prefix'  => 'company',
            'keyPath' => 'company',
            'create'  => [
                'constraints' => [new Dictionary(), new NotBlank()],
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
            'prefix'  => 'storage',
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
             'password' => new UnsafeParameter('qwe'),
             'company'  => new UnsafeParameter(
                 ['name'    => new UnsafeParameter('bigsool'),
                  'storage' => new UnsafeParameter(
                      ['url' => new UnsafeParameter('http://www.bigsool.com')]),
                 ]),

            ]);

        $this->defineAction($mgrCompany, ['create',
                                          ['name' => [ERR_INVALID_NAME,
                                                      [
                                                          new Assert\NotBlank(),
                                                      ]
                                          ]
                                          ],
                                          function (ActionContext $context) use (&$self, &$called, &$mgrCompany) {

                                              $params = $context->getParams();
                                              $self->assertCount(2, $params);
                                              $self->assertArrayHasKey('name', $params);
                                              $self->assertSame('bigsool', $params['name']);
                                              $self->assertArrayHasKey('storage', $params);
                                              $self->assertInstanceOf('\Core\Parameter\UnsafeParameter',
                                                                      $params['storage']);
                                              $storageParams = $params['storage']->getValue();
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
         * @var User $user
         */
        $user = $this->magicalAction('Create', $mgrUser, [$actionContext]);

        $user->setOwnedCompany($user->getCompany());
        $user->getCompany()->setOwner($user);
        ApplicationContext::getInstance()->getNewRegistry()->save($user);

        $this->assertTrue($called);
        $this->assertInstanceOf(Registry::realModelClassName('TestUser'), $user);
        $this->assertSame('thomas@bigsool.com', $user->getEmail());
        $this->assertSame(UserHelper::encryptPassword($user->getSalt(), 'qwe'), $user->getPassword());
        $this->assertSame('bigsool', $user->getCompany()->getName());
        $this->assertContainsOnly($user, $user->getCompany()->getUsers());

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
        $appCtx->setProduct('Archipad');

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
            'prefix'  => 'company',
            'keyPath' => 'company',
            'update'  => [
                'constraints' => [new Dictionary(), new NotBlank()],
            ]
        ]);

        $this->addAspect($mgrUser, [
            'model'   => 'TestStorage',
            'prefix'  => 'storage',
            'keyPath' => 'company.storage',
            'update'  => [
                'constraints' => [new Null()],
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
             'company'   => new UnsafeParameter(['name' => 'bigsoole']),
             'storage'   => ['url' => 'http://www.bigsoole.com']
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

        $this->assertSame('bigsoole', $user->getCompany()->getName());


        $this->assertSame('http://www.bigsoole.com', $user->getCompany()->getStorage()->getUrl());

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
            'prefix'  => 'company',
            'keyPath' => 'company',
            'update'  => [
                'constraints' => [new Dictionary(), new NotBlank()],
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
            'prefix'  => 'storage',
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
             'company'   => new UnsafeParameter(['name'    => 'bigsoolee',
                                                 'storage' => ['url' => 'http://www.bigsoolee.com']
                                                ])
            ]);

        $this->defineAction($mgrCompany, ['update',
                                          ['name' => [ERR_INVALID_NAME,
                                                      [
                                                          new Assert\NotBlank(),
                                                      ]
                                          ]
                                          ],
                                          function (ActionContext $context) use (&$self, &$called, &$mgrCompany) {

                                              $params = $context->getParams();
                                              // TODO: check parameter id
                                              $self->assertCount(3, $params);
                                              $self->assertArrayHasKey('name', $params);
                                              $self->assertSame('bigsoolee', $params['name']);
                                              $self->assertArrayHasKey('storage', $params);
                                              $storageParams = $params['storage'];
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
            'prefix'  => 'company',
        ]);

        $this->addAspect($mgrUser, [
            'model'   => 'TestStorage',
            'keyPath' => 'company.storage',
            'prefix'  => 'storage',
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

        $result = $this->magicalAction('Find', $mgrUser, [$values, $alias, $filters]);
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) == 1);
        $account = $result[0];
        $this->assertInstanceOf('\Core\Model\TestAccount', $account);
        $this->assertInstanceOf('\Core\Model\TestCompany', $account->getCompany());
        $this->assertEquals('u1@bigsool.com', $account->getUser()->getEmail());
        $this->assertEquals('Bigsool', $account->getCompany()->getName());
        $this->assertEquals('http://www.amazon.com/', $account->getStorage()->getUrl());

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
            'prefix'  => 'company',
        ]);

        $this->addAspect($mgrUser, [
            'model'   => 'TestStorage',
            'keyPath' => 'company.storage',
            'prefix'  => 'storage',
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

        $result = $this->magicalAction('Find', $mgrUser, [$values, $alias, $filters, true]);
        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) == 1);
        $result = $result[0];
        $this->assertTrue(is_array($result));
        $this->assertEquals('Bigsool', $result['company']['name']);
        $this->assertEquals('http://www.amazon.com/', $result['company']['storage']['url']);

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

        $result = $this->magicalAction('Find', $mgrUser, [['user.*'], [], $filters, true]);

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

        $result = $this->magicalAction('Find', $mgrCompany, [['company.*'], [],[], true]);

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

        $result = $this->magicalAction('Find', $mgrUser, [['user.*'], [], $filters, true]);

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

        $result = $this->magicalAction('Find', $mgrCompany, [['company.*'], [], $filters, true]);

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

}
