<?php


namespace Core\Action;


use Core\Module\Test\User\ModuleManager as UserModuleManager;
use Core\Registry;
use Core\TestCase;

class BasicCreateActionTest extends TestCase {

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        self::resetDatabase(self::getApplicationContext());

    }

    public function testConstructor () {

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Core');

        $userModuleManager = new UserModuleManager();
        $userModuleManager->loadHelpers($appCtx);

        $preCalled = false;
        $postCalled = false;

        $action = new BasicCreateAction('module', 'TestUser', 'UserFeatureHelper', [], [], function () use (&$preCalled) {

            $preCalled = true;

        }, function () use (&$postCalled) {

            $postCalled = true;

        });

        $actCtx = $this->getActionContext();
        $actCtx->setParams(['email' => 'qwe@qwe.com', 'password' => 'qwe']);
        $user = $action->process($actCtx);

        $this->assertInstanceOf(Registry::realModelClassName('TestUser'), $user);
        $this->assertSame($user, $actCtx['testUser']);

        $this->assertTrue($preCalled);
        $this->assertTrue($postCalled);

    }

    /**
     * @expectedException \Exception
     */
    public function testWrongHelper () {

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Core');

        $userModuleManager = new UserModuleManager();
        $userModuleManager->loadHelpers($appCtx);

        (new BasicCreateAction('module', 'TestCompany', 'UserFeatureHelper', [], [], function () use (&$preCalled) {

            $preCalled = true;

        }, function () use (&$postCalled) {

            $postCalled = true;

        }))->process($this->getActionContext());

    }

}