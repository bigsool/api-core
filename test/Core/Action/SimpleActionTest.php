<?php


namespace Core\Action;


use Core\Auth;
use Core\Context\ActionContext;
use Core\Error\FormattedError;
use Core\Parameter\UnsafeParameter;
use Core\TestCase;
use Core\Validation\Parameter\Int;
use Core\Validation\Parameter\String;
use Core\Validation\RuntimeConstraintsProvider;

class SimpleActionTest extends TestCase {

    public function testGetModule () {

        $module = 'TestUser';
        $action = new SimpleAction($module, 'qwe', NULL, [], $this->getCallable());
        $this->assertSame($module, $action->getModule());

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidModule () {

        new SimpleAction('', 'qwe', NULL, [], $this->getCallable());

    }

    public function testGetName () {

        $name = 'CreateUser';
        $action = new SimpleAction('qwe', $name, NULL, [], $this->getCallable());
        $this->assertSame($name, $action->getName());

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidName () {

        new SimpleAction('qwe', NULL, NULL, [], $this->getCallable());

    }

    public function testValidate () {

        $errorManager = $this->getApplicationContext()->getErrorManager();

        $constraintsProviderA = new RuntimeConstraintsProvider(['a' => [new String()]]);
        $constraintsProviderB = new RuntimeConstraintsProvider(['b' => [new Int()]]);

        $params = ['a' => [$constraintsProviderA]];
        $context = $this->getActionContext();
        $context->setParam('a', new UnsafeParameter('A', 'a'));
        $context->setParam('b', new UnsafeParameter('B', 'b'));

        $action = new SimpleAction('module', 'name', NULL, $params, $this->getCallable());

        // No exceptions
        $action->validate($context);

        $params['b'] = [$constraintsProviderB, true];

        $action = new SimpleAction('module', 'name', NULL, $params, $this->getCallable());
        try {
            $action->validate($context);
            $this->fail('SimpleAction->validate() should throw an Exception');
        }
        catch (FormattedError $e) {
            $expectedErrorCode = (new Int())->getErrorCode();;
            $this->assertSame($expectedErrorCode, $e->getCode());
        }

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidParam () {

        new SimpleAction('module', 'name', NULL, ['qwe' => []], $this->getCallable());

    }

    public function testProcess () {

        $module = 'module';
        $name = 'name';
        $called = false;
        $self = $this;
        $ctx = $this->getActionContext();
        /**
         * @var SimpleAction $action
         */
        $action = $this->getMock('\Core\Action\SimpleAction', ['validate', 'authorize'],
                                 [$module,
                                  $name,
                                  NULL,
                                  [],
                                  function (ActionContext $context) use (
                                      &$called, &$self,
                                      &$action, &$ctx
                                  ) {

                                      /**
                                       * @var Action $this
                                       */
                                      $self->assertSame($action,
                                                        $this);
                                      $self->assertSame($ctx,
                                                        $context);

                                      $called = true;

                                  }
                                 ]);

        $action->expects($this->once())->method('validate')->with($this->equalTo($ctx));
        $action->expects($this->once())->method('authorize')->with($this->equalTo($ctx));
        $action->process($ctx);

        $this->assertTrue($called);

    }

    public function testAuthorize () {

        $reqCtx = $this->getRequestContext();
        $action = new SimpleAction('module', 'name', Auth::GUEST, [], $this->getCallable());
        $this->assertTrue($action->authorize($reqCtx->getNewActionContext()));

        /**
         * @var Auth $auth
         */
        $rights = [Auth::AUTHENTICATED];
        $auth = $this->getMock('\Core\Auth', ['hasRights']);
        $auth->method('hasRights')->with($this->equalTo($rights))->willReturn(true);
        $reqCtx->setAuth($auth);
        $action = new SimpleAction('module', 'name', $rights, [], $this->getCallable());
        $this->assertTrue($action->authorize($reqCtx->getNewActionContext()));

    }

    /**
     * @expectedException \Core\Error\FormattedError
     * @expectedExceptionCode -7
     */
    public function testAuthorizeFail () {

        self::resetApplicationContext();
        self::getApplicationContext(); // Configure ApplicationContext

        $reqCtx = $this->getRequestContext();
        $action = new SimpleAction('module', 'name', Auth::AUTHENTICATED, [], $this->getCallable());
        $action->authorize($reqCtx->getNewActionContext());

    }

} 