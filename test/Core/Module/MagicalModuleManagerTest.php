<?php


namespace Core\Module;


use Core\Action\ActionReference;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\RequestContext;
use Core\Model\User;
use Core\Module\CompanyFeature\ModuleManager as CompanyModuleManager;
use Core\Module\StorageFeature\ModuleManager as StorageModuleManager;
use Core\Module\UserFeature\Helper as UserHelper;
use Core\Module\UserFeature\ModuleManager as UserModuleManager;
use Core\Parameter\SafeParameter;
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

    public function testMinimalAddAspect () {

        $mgr = $this->getMockMagicalModuleManager();
        $this->addUserAspect($mgr);

        $modelAspects = $this->getAspects($mgr);
        $this->assertInternalType('array', $modelAspects);
        $this->assertContainsOnlyInstancesOf('\Core\Module\ModelAspect', $modelAspects);
        $this->assertCount(1, $modelAspects);

        $modelAspect = $modelAspects[0];
        $this->assertNull($modelAspect->getPrefix());
        $this->assertSame('User', $modelAspect->getModel());
        $this->assertSame([], $modelAspect->getConstraints());
        $this->assertNull($modelAspect->getKeyPath());

    }

    protected function addUserAspect (MagicalModuleManager &$mgr) {

        $this->addAspect($mgr, [
            'model' => 'User',
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
     * @param array                $params
     */
    protected function setMainEntity (MagicalModuleManager &$mgr, array $params) {

        $method = (new \ReflectionClass($mgr))->getMethod('setMainEntity');
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
            'model'  => 'User',
            'prefix' => new \stdClass(),
        ]);

    }

    /**
     * @expectedException \Exception
     */
    public function testAddAspectInvalidKeyPath () {

        $mgr = $this->getMockMagicalModuleManager();
        $this->addAspect($mgr, [
            'model'   => 'User',
            'keyPath' => 'qwe qwe',
        ]);

    }

    /**
     * @expectedException \Exception
     */
    public function testAddAspectInvalidConstraints () {

        $mgr = $this->getMockMagicalModuleManager();
        $this->addAspect($mgr, [
            'model'       => 'User',
            'constraints' => [new \stdClass()],
        ]);

    }

    /**
     * @expectedException \Exception
     */
    public function testAddAspectInvalidActions () {

        $mgr = $this->getMockMagicalModuleManager();
        $this->addAspect($mgr, [
            'model'   => 'User',
            'actions' => ['create' => new \stdClass()],
        ]);

    }

    /**
     * @expectedException \Exception
     */
    public function testAddTwoMainEntitiesAspect () {

        $mgr = $this->getMockMagicalModuleManager();
        $this->addAspect($mgr, [
            'model' => 'User',
        ]);
        $this->addAspect($mgr, [
            'model' => 'Company',
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
        $this->assertSame('User', $modelAspect->getModel());
        $this->assertSame([], $modelAspect->getConstraints());
        $this->assertNull($modelAspect->getKeyPath());

        $modelAspect = $modelAspects[1];
        $this->assertSame('company', $modelAspect->getPrefix());
        $this->assertSame('Company', $modelAspect->getModel());
        $keyPath = $modelAspect->getKeyPath();
        $this->assertInstanceOf('\Core\Expression\AbstractKeyPath', $keyPath);
        $this->assertSame('company', $keyPath->getValue());
        $validators = $modelAspect->getConstraints();
        $this->assertInternalType('array', $validators);
        $this->assertContainsOnlyInstancesOf('\Symfony\Component\Validator\Constraint', $validators);
        $this->assertCount(2, $validators);
        $this->assertInstanceOf('\Core\Validation\Constraints\Dictionary', $validators[0]);
        $this->assertInstanceOf('\Symfony\Component\Validator\Constraints\NotBlank', $validators[1]);

        $modelAspect = $modelAspects[2];
        $this->assertSame('storage', $modelAspect->getPrefix());
        $this->assertSame('Storage', $modelAspect->getModel());
        $keyPath = $modelAspect->getKeyPath();
        $this->assertInstanceOf('\Core\Expression\AbstractKeyPath', $keyPath);
        $this->assertSame('company.storage', $keyPath->getValue());
        $validators = $modelAspect->getConstraints();
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
            'model'       => 'Company',
            'prefix'      => 'company',
            'keyPath'     => 'company',
            'constraints' => [new Dictionary(), new NotBlank()],
        ]);

    }

    /**
     * @param MagicalModuleManager $mgr
     */
    protected function addStorageAspect (MagicalModuleManager &$mgr) {

        $this->addAspect($mgr, [
            'model'       => 'Storage',
            'prefix'      => 'storage',
            'keyPath'     => 'company.storage',
            'constraints' => [new Null()],
            'actions'     => ['create' => NULL],
        ]);

    }

    public function AddAspectOneToMany () {

        $mgr = $this->getMockMagicalModuleManager();

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();

        $this->addAspect($mgr, [
            'model' => 'Company',
        ]);
        $this->addAspect($mgr, [
            'model'   => 'User',
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
                                                            'param2' => new SafeParameter(new \DateTime())
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

        $mgr = $this->getMockMagicalModuleManager();

        $userModuleManager = new UserModuleManager();

        $this->setMainEntity($mgr, [
            'model' => 'User',
        ]);


        $appCtx = ApplicationContext::getInstance();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);

        $actionContext = $this->getActionContextWithParams(
            ['email'    => new SafeParameter('qwe@qwe1.com'),
             'password' => new UnsafeParameter('qwe')
            ]);

        /**
         * @var User $user
         */
        $user = $this->magicalCreate($mgr, [$actionContext]);
        $this->assertInstanceOf(Registry::realModelClassName('User'), $user);
        $this->assertSame('qwe@qwe1.com', $user->getEmail());
        $this->assertSame(UserHelper::encryptPassword($user->getSalt(), 'qwe'), $user->getPassword());

    }

    /**
     * @param MagicalModuleManager $mgr
     * @param array                $args
     *
     * @return mixed
     */
    protected function magicalCreate (MagicalModuleManager &$mgr, array $args = []) {

        $method = (new \ReflectionClass($mgr))->getMethod('magicalCreate');
        $method->setAccessible(true);

        return $method->invokeArgs($mgr, $args);

    }

    /**
     * @param MagicalModuleManager $mgr
     * @param array                $args
     *
     * @return mixed
     */
    protected function magicalUpdate (MagicalModuleManager &$mgr, array $args = []) {

        $method = (new \ReflectionClass($mgr))->getMethod('magicalUpdate');
        $method->setAccessible(true);

        return $method->invokeArgs($mgr, $args);

    }

    public function testComplexMagicalCreate () {

        $mgr = $this->getMockMagicalModuleManager();

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();
        $this->setMainEntity($mgr, [
            'model' => 'User',
        ]);
        $this->addCompanyAspect($mgr);
        // $this->addStorageAspect($mgr);


        $appCtx = ApplicationContext::getInstance();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);
        $companyModuleManager->loadActions($appCtx);
        $companyModuleManager->loadHelpers($appCtx);
        // $storageModuleManager->loadActions($appCtx);
        // $storageModuleManager->loadHelpers($appCtx);

        $actionContext = $this->getActionContextWithParams(
            [
                'email'    => new SafeParameter('qwe@qwe2.com'),
                'name'     => new SafeParameter('thierry'),
                'password' => new UnsafeParameter('qwe'),
                'company'  => new SafeParameter(['name' => new UnsafeParameter('bigsool')])
            ]);

        /**
         * @var User $user
         */
        $user = $this->magicalCreate($mgr, [$actionContext]);

        $this->assertInstanceOf(Registry::realModelClassName('User'), $user);
        $this->assertSame('qwe@qwe2.com', $user->getEmail());
        $this->assertSame(UserHelper::encryptPassword($user->getSalt(), 'qwe'), $user->getPassword());
        $this->assertSame('bigsool', $user->getCompany()->getName());
        $this->assertContainsOnly($user, $user->getCompany()->getUsers());

    }

    public function testMagicalCreateWithTwoMagicalModuleManager () {

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();

        $mgrUser = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgrUser->method('getModuleName')->willReturn('UserModule');
        $this->setMainEntity($mgrUser, [
            'model' => 'User',
        ]);


        $this->addAspect($mgrUser, [
            'model'       => 'Company',
            'prefix'      => 'company',
            'keyPath'     => 'company',
            'constraints' => [new Dictionary(), new NotBlank()],
            'actions'     => ['create' => new ActionReference('Archipad\\Group', 'create')],
        ]);

        $mgrCompany = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgrCompany->method('getModuleName')->willReturn('Archipad\Group');
        $this->setMainEntity($mgrCompany, [
            'model' => 'Company',
        ]);
        $this->addAspect($mgrCompany, [
            'model'   => 'Storage',
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
            ['email'    => new SafeParameter('qwe@qwe.com'),
             'name'     => new SafeParameter('thierry'),
             'password' => new UnsafeParameter('qwe'),
             'company'  => new UnsafeParameter(
                 ['name'    => new UnsafeParameter('bigsool'),
                  'storage' => new UnsafeParameter(
                      ['url' => new UnsafeParameter('http://ddfd.fr')]),
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
                                              $self->assertSame('bigsool', $params['name']->getValue());
                                              $called = true;

                                              return $self->magicalCreate($mgrCompany, [$context]);

                                          }
        ]);

        /**
         * @var User $user
         */
        $user = $this->magicalCreate($mgrUser, [$actionContext]);

        $this->assertTrue($called);
        $this->assertInstanceOf(Registry::realModelClassName('User'), $user);
        $this->assertSame('qwe@qwe.com', $user->getEmail());
        $this->assertSame(UserHelper::encryptPassword($user->getSalt(), 'qwe'), $user->getPassword());
        $this->assertSame('bigsool', $user->getCompany()->getName());
        $this->assertContainsOnly($user, $user->getCompany()->getUsers());

    }

    /**
     * @depends testMagicalCreateWithTwoMagicalModuleManager
     */
    public function testSimpleMagicalUpdate () {

        $mgr = $this->getMockMagicalModuleManager();

        $userModuleManager = new UserModuleManager();

        $this->setMainEntity($mgr, [
            'model' => 'User',
        ]);

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);

        $actionContext = $this->getActionContextWithParams(
            ['id'        => new SafeParameter(1),
             'email'     => new SafeParameter('youpy@qwe.com'),
             'name'      => new SafeParameter('youpy'),
             'firstname' => new SafeParameter('youpy'),
             'password'  => new SafeParameter('youpy'),
            ]);

        /**
         * @var User $user
         */
        $user = $this->magicalUpdate($mgr, [$actionContext]);
        $this->assertInstanceOf(Registry::realModelClassName('User'), $user);
        $this->assertSame('youpy@qwe.com', $user->getEmail());
        $this->assertSame('youpy', $user->getName());
        $this->assertSame('youpy', $user->getFirstname());
        $this->assertSame('youpy', $user->getPassword());
    }

    /**
     * @depends testMagicalCreateWithTwoMagicalModuleManager
     */
    public function testComplexMagicalUpdate () {


        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();


        $mgrUser = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgrUser->method('getModuleName')->willReturn('UserModule');

        $this->setMainEntity($mgrUser, [
            'model' => 'User',
        ]);


        $this->addAspect($mgrUser, [
            'model'       => 'Company',
            'prefix'      => 'company',
            'keyPath'     => 'company',
            'constraints' => [new Dictionary(), new NotBlank()],
            'actions'     => ['update' => new ActionReference('Archipad\\Group', 'update')],
        ]);

        $mgrCompany = $this->getMockMagicalModuleManager(['getModuleName']);
        $mgrCompany->method('getModuleName')->willReturn('Archipad\Group');
        $this->setMainEntity($mgrCompany, [
            'model' => 'Company',
        ]);

        $this->addAspect($mgrCompany, [
            'model'   => 'Storage',
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
            ['id'        => new SafeParameter(1),
             'email'     => new SafeParameter('youpy@qwe.com'),
             'name'      => new SafeParameter('youpy'),
             'firstname' => new SafeParameter('youpy'),
             'password'  => new SafeParameter('youpy'),
             'company'   => new UnsafeParameter(['name'    => new SafeParameter('bigsoole'),
                                                 'storage' => new SafeParameter(['url' => new SafeParameter('http://www.bigsoole.com')])
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
                                              $self->assertCount(3, $params);
                                              $self->assertArrayHasKey('name', $params);
                                              $self->assertSame('bigsoole', $params['name']->getValue());
                                              $called = true;

                                              return $self->magicalUpdate($mgrCompany, [$context]);

                                          }
        ]);
        /*
         * @var User $user
         */
        $user = $this->magicalUpdate($mgrUser, [$actionContext]);
        $this->assertInstanceOf(Registry::realModelClassName('User'), $user);
        $this->assertSame('youpy@qwe.com', $user->getEmail());
        $this->assertSame('youpy', $user->getName());
        $this->assertSame('youpy', $user->getFirstname());
        $this->assertSame('youpy', $user->getPassword());

        $this->assertSame('bigsoole', $user->getCompany()->getName());


        $this->assertSame('http://www.bigsoole.com', $user->getCompany()->getStorage()->getUrl());

    }

    protected function tearDown () {

        $whiteList =
            ['testMagicalCreateWithTwoMagicalModuleManager', 'testSimpleMagicalUpdate', 'testComplexMagicalUpdate'];
        $currentTestFcName = $this->getName();
        if (!in_array($currentTestFcName, $whiteList)) {
            $this->rollBackDatabase();
        }
        else {
            $this->commitDB();
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

    }

} 