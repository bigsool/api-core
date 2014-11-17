<?php


namespace Core\Action;


use Core\Auth;
use Core\Context\ActionContext;
use Core\Error\Error;
use Core\Error\FormattedError;
use Core\Parameter\UnsafeParameter;
use Core\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class SimpleActionTest extends TestCase {

    public function testGetModule () {

        $module = 'User';
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

        $errorCodeA = rand();
        $errorCodeB = rand();
        $errorManager->defineError(new Error($errorCodeA, 'fr', 'en', 1));
        $errorManager->defineError(new Error($errorCodeB, 'fr', 'en', 1));

        $constraintsProviderA = $this->getMockConstraintsProvider();
        $constraintsProviderB = $this->getMockConstraintsProvider();

        $params = ['a' => [$errorCodeA, $constraintsProviderA]];
        $context = $this->getActionContext();
        $context->setParam('a', new UnsafeParameter('A'));
        $context->setParam('b', new UnsafeParameter('B'));

        $calledA = false;
        $constraintsProviderA->method('validate')->will($this->returnCallback(function ($field, $value,
                                                                                        $forceOptional) use (
            &$calledA
        ) {

            $calledA = true;
            $this->assertSame('a', $field);
            $this->assertSame('A', $value);
            $this->assertFalse($forceOptional);

            return new ConstraintViolationList();

        }));

        $action = new SimpleAction('module', 'name', NULL, $params, $this->getCallable());

        // No exceptions
        $action->validate($context);

        $params['b'] = [$errorCodeB, $constraintsProviderB, true];

        $calledB = false;
        $constraintsProviderB->method('validate')->will($this->returnCallback(function ($field, $value,
                                                                                        $forceOptional) use (
            &$calledB
        ) {

            $calledB = true;
            $this->assertSame('b', $field);
            $this->assertSame('B', $value);
            $this->assertTrue($forceOptional);

            $violations = new ConstraintViolationList();
            $violations->add(new ConstraintViolation('', '', [], '', '', ''));

            return $violations;

        }));

        $action = new SimpleAction('module', 'name', NULL, $params, $this->getCallable());
        try {
            $action->validate($context);
            $this->fail('SimpleAction->validate() should throw an Exception');
        }
        catch (FormattedError $e) {
            $this->assertSame(1, $e->getCode());
            $this->assertCount(1, $e->getChildErrors());
            $this->assertSame($errorCodeB, $e->getChildErrors()[0]->getCode());
        }

        $this->assertTrue($calledA);
        $this->assertTrue($calledB);

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
     * @expectedExceptionCode 7
     */
    public function testAuthorizeFail () {

        self::resetApplicationContext();
        self::getApplicationContext(); // Configure ApplicationContext

        $reqCtx = $this->getRequestContext();
        $action = new SimpleAction('module', 'name', Auth::AUTHENTICATED, [], $this->getCallable());
        $action->authorize($reqCtx->getNewActionContext());

    }

} 