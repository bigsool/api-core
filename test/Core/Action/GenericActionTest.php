<?php


namespace Core\Action;


use Core\Action\GenericAction as Action;
use Core\Context\ActionContext;
use Core\TestCase;

class GenericActionTest extends TestCase {

    public function testGetModule () {

        $module = 'User';
        $action = new Action($module, 'qwe', $this->getCallable(), $this->getCallable(), $this->getCallable());
        $this->assertSame($module, $action->getModule());

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidModule () {

        new Action('', 'qwe', $this->getCallable(), $this->getCallable(), $this->getCallable());

    }

    public function testGetName () {

        $name = 'CreateUser';
        $action = new Action('qwe', $name, $this->getCallable(), $this->getCallable(), $this->getCallable());
        $this->assertSame($name, $action->getName());

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidName () {

        new Action('qwe', NULL, $this->getCallable(), $this->getCallable(), $this->getCallable());

    }

    public function testAuthorize () {

        $module = 'module';
        $name = 'name';
        $authorizeCalled = false;
        $validateCalled = false;
        $processCalled = false;
        $self = $this;
        $ctx = $this->getActionContext();
        $action =
            new Action($module, $name, $this->getTestedCallable($authorizeCalled, $self, $action, $ctx),
                       $this->getTestedCallable($validateCalled, $self, $action, $ctx),
                       $this->getTestedCallable($processCalled, $self, $action, $ctx));

        $action->authorize($ctx);

        $this->assertTrue($authorizeCalled);
        $this->assertFalse($validateCalled);
        $this->assertFalse($processCalled);

    }

    protected function getTestedCallable (&$called, &$self, &$action, &$ctx) {

        return function (ActionContext $context) use (&$called, &$self, &$action, &$ctx) {

            /**
             * @var Action $this
             */
            $self->assertSame($action, $this);
            $self->assertSame($ctx, $context);

            $called = true;

        };

    }

    public function testValidate () {

        $module = 'module';
        $name = 'name';
        $authorizeCalled = false;
        $validateCalled = false;
        $processCalled = false;
        $self = $this;
        $ctx = $this->getActionContext();
        $action =
            new Action($module, $name, $this->getTestedCallable($authorizeCalled, $self, $action, $ctx),
                       $this->getTestedCallable($validateCalled, $self, $action, $ctx),
                       $this->getTestedCallable($processCalled, $self, $action, $ctx));

        $action->validate($ctx);

        $this->assertFalse($authorizeCalled);
        $this->assertTrue($validateCalled);
        $this->assertFalse($processCalled);

    }

    public function testProcess () {

        $module = 'module';
        $name = 'name';
        $authorizeCalled = false;
        $validateCalled = false;
        $processCalled = false;
        $self = $this;
        $ctx = $this->getActionContext();
        $action =
            new Action($module, $name, $this->getTestedCallable($authorizeCalled, $self, $action, $ctx),
                       $this->getTestedCallable($validateCalled, $self, $action, $ctx),
                       $this->getTestedCallable($processCalled, $self, $action, $ctx));

        $action->process($ctx);

        $this->assertTrue($authorizeCalled);
        $this->assertTrue($validateCalled);
        $this->assertTrue($processCalled);

    }

} 