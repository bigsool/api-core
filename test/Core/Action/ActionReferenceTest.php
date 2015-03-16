<?php

namespace Core\Action;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\TestCase;

class ActionReferenceTest extends TestCase {

    /**
     * @expectedException \Exception
     */
    public function testInvalidModule () {

        new ActionReference('', 'qwe', NULL, [], $this->getCallable());

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidName () {

        new ActionReference('qwe', NULL, NULL, [], $this->getCallable());

    }

    public function testAuthorize () {

        $this->resetApplicationContext();
        $module = 'module';
        $name = 'name';
        $authorizeCalled = false;
        $validateCalled = false;
        $processCalled = false;
        $self = $this;
        $ctx = $this->getActionContext();
        ApplicationContext::getInstance()->addAction(
            $action =
                new GenericAction($module, $name, $this->getTestedCallable($authorizeCalled, $self, $action, $ctx),
                                  $this->getTestedCallable($validateCalled, $self, $action, $ctx),
                                  $this->getTestedCallable($processCalled, $self, $action, $ctx)));

        (new ActionReference($module, $name))->authorize($ctx);

        $this->assertTrue($authorizeCalled);
        $this->assertFalse($validateCalled);
        $this->assertFalse($processCalled);

    }

    /**
     * @param bool                $called
     * @param ActionReferenceTest $self
     * @param Action              $action
     * @param ActionContext       $ctx
     *
     * @return callable
     */
    protected function getTestedCallable (&$called, &$self, &$action, &$ctx) {

        return function (ActionContext $context, Action $_action) use (&$called, &$self, &$action, &$ctx) {

            $self->assertSame($action, $_action);
            $self->assertSame($ctx, $context);

            $called = true;

        };

    }

    public function testValidate () {

        $this->resetApplicationContext();
        $module = 'module';
        $name = 'name';
        $authorizeCalled = false;
        $validateCalled = false;
        $processCalled = false;
        $self = $this;
        $ctx = $this->getActionContext();
        ApplicationContext::getInstance()->addAction(
            $action =
                new GenericAction($module, $name, $this->getTestedCallable($authorizeCalled, $self, $action, $ctx),
                                  $this->getTestedCallable($validateCalled, $self, $action, $ctx),
                                  $this->getTestedCallable($processCalled, $self, $action, $ctx)));

        (new ActionReference($module, $name))->validate($ctx);

        $this->assertFalse($authorizeCalled);
        $this->assertTrue($validateCalled);
        $this->assertFalse($processCalled);

    }

    public function testProcess () {

        $this->resetApplicationContext();
        $module = 'module';
        $name = 'name';
        $authorizeCalled = false;
        $validateCalled = false;
        $processCalled = false;
        $self = $this;
        $ctx = $this->getActionContext();
        ApplicationContext::getInstance()->addAction(
            $action =
                new GenericAction($module, $name, $this->getTestedCallable($authorizeCalled, $self, $action, $ctx),
                                  $this->getTestedCallable($validateCalled, $self, $action, $ctx),
                                  $this->getTestedCallable($processCalled, $self, $action, $ctx)));

        (new ActionReference($module, $name))->process($ctx);

        $this->assertTrue($authorizeCalled);
        $this->assertTrue($validateCalled);
        $this->assertTrue($processCalled);

    }

    /**
     * @expectedException \Exception
     */
    public function testNotDefinedAction () {

        $this->resetApplicationContext();
        (new ActionReference('qwe', 'qwe'))->process($this->getActionContext());

    }

}