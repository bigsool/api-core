<?php


namespace Core\Action;

use Core\Context\RequestContext;
use Core\Model\TestUser;
use Core\Model\User;
use Core\Module\TestCompany\ModuleManager as CompanyModuleManager;
use Core\Module\TestUser\ModuleManager;
use Core\Module\TestUser\ModuleManager as UserModuleManager;
use Core\Registry;
use Core\TestCase;

class BasicUpdateActionTest extends TestCase {

    /**
     * @var TestUser
     */
    private static $user;

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        $appCtx = self::getApplicationContext();
        self::resetDatabase($appCtx);

        $userModuleManager = new ModuleManager();

        $moduleEntities = $userModuleManager->createModuleEntityDefinitions($appCtx);
        $moduleEntity = $moduleEntities[0];
        $appCtx->addModuleEntity($moduleEntity);

        $actCtx = $appCtx->getActionContext(new RequestContext(), '', '');
        self::$user = $user = $moduleEntity->create($actCtx, ['email' => 'qwe@qwe.com', 'password' => 'qwe']);
        $moduleEntity->save($user);

    }

    public function testConstructor () {

        $appCtx = $this->getApplicationContext();

        $userModuleManager = new UserModuleManager();
        $userModuleManager->loadHelpers($appCtx);

        $moduleEntities = $userModuleManager->createModuleEntityDefinitions($appCtx);
        $moduleEntity = $moduleEntities[0];
        $appCtx->addModuleEntity($moduleEntity);

        $preCalled = false;
        $postCalled = false;

        $action =
            new BasicUpdateAction('Core\TestUser', $moduleEntity, NULL, [], function () use (&$preCalled) {

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

        $companyModuleManager = new CompanyModuleManager();
        $companyModuleManager->loadHelpers($appCtx);

        $moduleEntities = $companyModuleManager->createModuleEntityDefinitions($appCtx);
        $moduleEntity = $moduleEntities[0];
        $appCtx->addModuleEntity($moduleEntity);

        $actCtx = $this->getActionContext();
        $actCtx->setParams(['id' => 1]);

        (new BasicUpdateAction('Core\TestCompany', $moduleEntity, [], [], function () use (&$preCalled) {

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

        $userModuleManager = new UserModuleManager();
        $userModuleManager->loadHelpers($appCtx);

        $moduleEntities = $userModuleManager->createModuleEntityDefinitions($appCtx);
        $moduleEntity = $moduleEntities[0];
        $appCtx->addModuleEntity($moduleEntity);

        $actCtx = $this->getActionContext();
        $actCtx->setParams(['id' => 567435453]);

        (new BasicUpdateAction('Core\TestUser', $moduleEntity, [], [], function () use (&$preCalled) {

            $preCalled = true;

        }, function () use (&$postCalled) {

            $postCalled = true;

        }))->process($actCtx);

    }

}