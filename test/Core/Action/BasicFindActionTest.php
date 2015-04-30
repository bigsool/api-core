<?php


namespace Core\Action;


use Core\Field\RelativeField;
use Core\Model\TestUser;
use Core\Module\TestUser\ModuleManager as UserModuleManager;
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

        $userModuleManager = new UserModuleManager();
        $userModuleManager->loadHelpers($appCtx);

        $preCalled = false;
        $postCalled = false;

        $action =
            new BasicFindAction('Core\TestUser', 'TestUser', 'UserFeatureHelper', [], [],
                function () use (&$preCalled) {

                    $preCalled = true;

                }, function () use (&$postCalled) {

                    $postCalled = true;

                });

        $actCtx = $this->getActionContext();
        $actCtx->setParams(['email' => 'thierry@bigsool.com']);
        $reqCtx = $actCtx->getRequestContext();
        $reqCtx->setReturnedFields([new RelativeField('email')]);


        $action->process($actCtx);

        /**
         * @var TestUser $user
         */
        $user = $actCtx['TestUser'][0];

        $this->assertInstanceOf('\Core\Model\TestUser', $user);

        $this->assertSame('thierry@bigsool.com', $user->getEmail());

        $this->assertTrue($preCalled);
        $this->assertTrue($postCalled);

    }

    /**
     * @expectedException \Exception
     */
    public function testWrongHelper () {

        $appCtx = $this->getApplicationContext();

        $userModuleManager = new UserModuleManager();
        $userModuleManager->loadHelpers($appCtx);

        (new BasicFindAction('Core\TestUser', 'TestUsere', 'UserFeatureHelper', [], [], function () use (&$preCalled) {

            $preCalled = true;

        }, function () use (&$postCalled) {

            $postCalled = true;

        }))->process($this->getActionContext());

    }

}