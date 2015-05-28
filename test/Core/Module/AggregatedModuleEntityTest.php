<?php


namespace Core\Module;


use Core\Action\GenericAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Module\TestCompany\ModuleManager as CompanyModuleManager;
use Core\Module\TestUser\ModuleManager as UserModuleManager;
use Core\Parameter\UnsafeParameter;
use Core\TestCase;
use Core\Validation\Parameter\Null;
use Core\Validation\Parameter\Object;
use Symfony\Component\Validator\Constraints\NotBlank;

class AggregatedModuleEntityTest extends TestCase {

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

    /**
     * @param MagicalModuleManager $mgr
     */
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
        $aggregatedEntity = $this->getMockAggregatedModuleEntity(['loadModelAspects']);
        $this->addAspect($mgr, [
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
        $context = $this->getActionContext();
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
        // TODO : //TODO : $appCtx->setProduct('Archipad');

        $userModuleManager->createActions($appCtx);
        $userModuleManager->loadHelpers($appCtx);
        $companyModuleManager->createActions($appCtx);
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

}