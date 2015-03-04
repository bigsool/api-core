<?php


namespace Core\Action;


use Core\Model\TestUser;
use Core\Module\TestUser\ModuleManager as UserModuleManager;
use Core\Registry;
use Core\TestCase;

class BasicCreateActionTest extends TestCase {

    /**
     * @var \Core\Model\TestUser
     */
    protected static $user;

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        self::resetDatabase(self::getApplicationContext());

        self::$user = new TestUser();
        self::$user->setEmail('thierry@bigsool.com');
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
            new BasicFindAction('Core\TestUser', 'TestUser', 'UserFeatureHelper', [], [], function () use (&$preCalled) {

                $preCalled = true;

            }, function () use (&$postCalled) {

                $postCalled = true;

            });

        $actCtx = $this->getActionContext();
        $actCtx->setParams(['email' => 'thierry@bigsool.com']);
        $action->process($actCtx);

        $user = $actCtx['TestUser'];

        $this->assertInstanceOf(Registry::realModelClassName('TestUser'), $user);
        $this->assertSame($user, $actCtx['testUser']);

        $this->assertTrue($preCalled);
        $this->assertTrue($postCalled);

    }

}