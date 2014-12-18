<?php


namespace Core\Module;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
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
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Null;

class MagicalModuleManagerTest extends TestCase {

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
            'model'   => 'User',
        ]);
        $this->addAspect($mgr, [
            'model'   => 'Company',
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

    protected function addCompanyAspect (MagicalModuleManager &$mgr) {

        $this->addAspect($mgr, [
            'model'       => 'Company',
            'prefix'      => 'company',
            'keyPath'     => 'company',
            'constraints' => [new Dictionary(), new NotBlank()],
        ]);

    }

    protected function addStorageAspect (MagicalModuleManager &$mgr) {

        $this->addAspect($mgr, [
            'model'       => 'Storage',
            'prefix'      => 'storage',
            'keyPath'     => 'company.storage',
            'constraints' => [new Null()],
            'actions'     => ['create' => NULL],
        ]);

    }

    public function testSimpleDefineAction () {

        self::resetApplicationContext();

        $this->getMockApplication();

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

        self::resetApplicationContext();

        $processFn = function (ActionContext $ctx) use (&$called) {

            $this->assertCount(3, $ctx->getParams());
            $called = true;

        };

        $mgr = $this->getMockMagicalModuleManager();
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

        $actionContext = $this->getMockActionContext();
        $actionContext->method('getParams')->willReturn(['params0' => new UnsafeParameter('qwe'),
                                                         'params1' => new UnsafeParameter('homme'),
                                                         'params2' => new SafeParameter(new \DateTime())
                                                        ]);

        $action->process($this->getMockActionContext());
        $this->assertTrue($called);

    }

    public function testSimpleMagicalCreate () {

        self::resetApplicationContext();

        $mgr = $this->getMockMagicalModuleManager();

        $userModuleManager = new UserModuleManager();

        $this->addUserAspect($mgr);

        $app = $this->getMockApplication();
        $app->method('getModuleManagers')
            ->willReturn([$mgr, $userModuleManager]);

        $appCtx = ApplicationContext::getInstance();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);

        $actionContext = $this->getMockActionContext();
        $actionContext->method('getParams')->willReturn(['email'    => new SafeParameter('invalid email forced'),
                                                         'password' => new UnsafeParameter('qwe')
                                                        ]);

        /**
         * @var User $user
         */
        $user = $this->magicalCreate($mgr, [$actionContext]);

        $this->assertInstanceOf(Registry::realModelClassName('User'), $user);
        $this->assertSame('invalid email forced', $user->getEmail());
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

    public function testComplexMagicalCreate () {

        self::resetApplicationContext();

        $mgr = $this->getMockMagicalModuleManager();

        $userModuleManager = new UserModuleManager();
        $companyModuleManager = new CompanyModuleManager();
        $storageModuleManager = new StorageModuleManager();

        $this->addUserAspect($mgr);
        $this->addCompanyAspect($mgr);
        $this->addStorageAspect($mgr);

        $app = $this->getMockApplication();
        $app->method('getModuleManagers')
            ->willReturn([$storageModuleManager, $mgr, $companyModuleManager, $userModuleManager]);

        $appCtx = ApplicationContext::getInstance();
        $appCtx->setProduct('Archipad');

        $userModuleManager->loadActions($appCtx);
        $companyModuleManager->loadActions($appCtx);

        $actionContext = $this->getMockActionContext();
        $actionContext->method('getParams')->willReturn(['email'    => new SafeParameter('qwe@qwe.com'),
                                                         'password' => new UnsafeParameter('qwe'),
                                                         'company'  => new UnsafeParameter(['name' => 'bigsool'])
                                                        ]);

        /**
         * @var User $user
         */
        $user = $this->magicalCreate($mgr, [$actionContext]);

        $this->assertInstanceOf(Registry::realModelClassName('User'), $user);
        $this->assertSame('invalid email forced', $user->getEmail());
        $this->assertSame(UserHelper::encryptPassword($user->getSalt(), 'qwe'), $user->getPassword());
        $this->assertSame('bigsool', $user->getCompany()->getName());

    }

} 