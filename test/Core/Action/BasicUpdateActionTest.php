<?php


namespace Core\Action;

use Core\Model\User;
use Core\Module\Test\Company\ModuleManager as CompanyModuleManager;
use Core\Module\Test\User\Helper as UserHelper;
use Core\Module\Test\User\ModuleManager as UserModuleManager;
use Core\Registry;
use Core\TestCase;

class BasicUpdateActionTest extends TestCase {

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        self::resetDatabase(self::getApplicationContext());

    }

    public function testConstructor () {

        $appCtx = $this->getApplicationContext();
        $appCtx->setProduct('Core');

        $userModuleManager = new UserModuleManager();
        $userModuleManager->loadHelpers($appCtx);

        /**
         * @var UserHelper $helper
         */
        $helper = $appCtx->getHelper('UserFeatureHelper');
        $createActCtx = $this->getActionContext();
        $helper->createTestUser($createActCtx, ['email' => 'qwe@qwe.com', 'password' => 'qwe']);
        /**
         * @var User $createdUser
         */
        $createdUser = $createActCtx['testUser'];

        $preCalled = false;
        $postCalled = false;

        $action =
            new BasicUpdateAction('module', 'TestUser', 'UserFeatureHelper', NULL, [], function () use (&$preCalled) {

                $preCalled = true;

            }, function () use (&$postCalled) {

                $postCalled = true;

            });

        $actCtx = $this->getActionContext();
        $actCtx->setParams(['id' => $createdUser->getId(), 'email' => 'qwe2@qwe.com']);
        /**
         * @var User $user
         */
        $user = $action->process($actCtx);

        $this->assertInstanceOf(Registry::realModelClassName('TestUser'), $user);
        $this->assertSame($user, $actCtx['testUser']);
        $this->assertSame('qwe2@qwe.com', $user->getEmail());
        $this->assertSame($createdUser->getId(), $user->getId());

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

        (new BasicUpdateAction('module', 'TestUser', 'CompanyFeatureHelper', [], [], function () use (&$preCalled) {

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

        (new BasicUpdateAction('module', 'TestUser', 'UserFeatureHelper', [], [], function () use (&$preCalled) {

            $preCalled = true;

        }, function () use (&$postCalled) {

            $postCalled = true;

        }))->process($actCtx);

    }

}