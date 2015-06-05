<?php


namespace Core\Action;


use Core\Context\RequestContext;
use Core\Field\RelativeField;
use Core\Model\TestUser;
use Core\Module\TestUser\ModuleManager as UserModuleManager;
use Core\Module\TestUser\ModuleManager;
use Core\TestCase;

class BasicFindActionTest extends TestCase {

    /**
     * @var \Core\Model\TestUser
     */
    protected static $user;

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        $appCtx = self::getApplicationContext();
        self::resetDatabase($appCtx);

        $userModuleManager = new ModuleManager();

        $moduleEntities = $userModuleManager->getModuleEntitiesName($appCtx);
        $moduleEntity = $moduleEntities[0];
        $appCtx->addModuleEntity($moduleEntity);

        $actCtx = $appCtx->getActionContext(new RequestContext(), '', '');
        $user = $moduleEntity->create($actCtx, ['email' => 'thierry@bigsool.com', 'password' => 'qwe']);
        $moduleEntity->save($user);

    }

    public function testConstructor () {

        $appCtx = $this->getApplicationContext();

        $userModuleManager = new UserModuleManager();
        $userModuleManager->loadHelpers($appCtx);

        $moduleEntities = $userModuleManager->getModuleEntitiesName($appCtx);
        $moduleEntity = $moduleEntities[0];
        $appCtx->addModuleEntity($moduleEntity);

        $preCalled = false;
        $postCalled = false;

        $action =
            new BasicFindAction('Core\TestUser', $moduleEntity, [], [], function () use (&$preCalled) {

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

}