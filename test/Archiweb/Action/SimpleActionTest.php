<?php


namespace Archiweb\Action;


use Archiweb\Context\ActionContext;
use Archiweb\Error\Error;
use Archiweb\Error\ErrorManager;
use Archiweb\Error\FormattedError;
use Archiweb\Parameter\UnsafeParameter;
use Archiweb\TestCase;
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

        ErrorManager::addDefinedError(new Error(ord('a'), 'fr', 'en'));
        ErrorManager::addDefinedError(new Error(ord('b'), 'fr', 'en'));

        $constraintsProviderA = $this->getMockConstraintsProvider();
        $constraintsProviderB = $this->getMockConstraintsProvider();

        $params = ['a' => [ord('a'), $constraintsProviderA]];
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

        $params['b'] = [ord('b'), $constraintsProviderB, true];

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
            $this->assertSame(ord('b'), $e->getCode());
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
        $action = $this->getMock('\Archiweb\Action\SimpleAction', ['validate', 'authorize'],
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

} 