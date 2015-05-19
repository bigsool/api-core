<?php


namespace Core\Action;


use Core\Context\ApplicationContext;
use Core\Module\TestUser\ModuleManager as UserModuleManager;
use Core\Registry;
use Core\TestCase;

class BasicCreationActionTest extends TestCase {

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        self::resetDatabase(self::getApplicationContext());

    }

    public function testConstructor () {

        $appCtx = $this->getApplicationContext();

        $userModuleManager = new UserModuleManager();
        $userModuleManager->load($appCtx);

        $preCalled = false;
        $postCalled = false;

        $action =
            new BasicCreateAction('Core\TestUser', $userModuleManager->getModuleEntity('TestUser'), [], [], function () use (&$preCalled) {

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

}