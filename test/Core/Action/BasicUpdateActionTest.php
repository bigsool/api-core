<?php


namespace Core\Action;

use Core\Model\TestUser;
use Core\Model\User;
use Core\Module\TestCompany\ModuleManager as CompanyModuleManager;
use Core\Module\TestUser\ModuleManager as UserModuleManager;
use Core\Registry;
use Core\TestCase;

class BasicUpdateActionTest extends TestCase {

    private static $user;

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        self::resetDatabase(self::getApplicationContext());


        self::$user = new TestUser();
        self::$user->setEmail('qwe@qwe.com');
        self::$user->setPassword('qwe');
        self::$user->setRegisterDate(new \DateTime());

        $registry = self::getApplicationContext()->getNewRegistry();
        $registry->save(self::$user);

    }

    public function testConstructor () {

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Core');

        $userModuleManager = new UserModuleManager();
        $userModuleManager->loadHelpers($appCtx);

        $preCalled = false;
        $postCalled = false;

        $action =
            new BasicUpdateAction('Core\TestUser', 'TestUser', 'UserFeatureHelper', NULL, [], function () use (&$preCalled) {

                $preCalled = true;

            }, function () use (&$postCalled) {

                $postCalled = true;

            });

        $actCtx = $this->getActionContext();
        $actCtx->setParams(['id' => self::$user->getId(), 'email' => 'qwe2@qwe.com']);
        /**
         * @var User $user
         */
        $user = $action->process($actCtx);

        $this->assertInstanceOf(Registry::realModelClassName('TestUser'), $user);
        $this->assertSame($user, $actCtx['testUser']);
        $this->assertSame('qwe2@qwe.com', self::$user->getEmail());
        $this->assertSame(self::$user->getId(), self::$user->getId());

        $this->assertTrue($preCalled);
        $this->assertTrue($postCalled);

    }

    /**
     * @depends testConstructor
     * @expectedException \Exception
     */
    public function testWrongHelper () {

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Core');

        $companyModuleManager = new CompanyModuleManager();
        $companyModuleManager->loadHelpers($appCtx);

        $actCtx = $this->getActionContext();
        $actCtx->setParams(['id' => 1]);

        (new BasicUpdateAction('Core\Company', 'TestUser', 'CompanyFeatureHelper', [], [], function () use (&$preCalled) {

            $preCalled = true;

        }, function () use (&$postCalled) {

            $postCalled = true;

        }))->process($actCtx);

    }

    /**
     * @expectedException \Exception
     */
    public function testWrongNumberOfEntity () {

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Core');

        $userModuleManager = new UserModuleManager();
        $userModuleManager->loadHelpers($appCtx);

        $actCtx = $this->getActionContext();
        $actCtx->setParams(['id' => 567435453]);

        (new BasicUpdateAction('Core\User', 'TestUser', 'UserFeatureHelper', [], [], function () use (&$preCalled) {

            $preCalled = true;

        }, function () use (&$postCalled) {

            $postCalled = true;

        }))->process($actCtx);

    }

}