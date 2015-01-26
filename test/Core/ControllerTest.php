<?php


namespace Core;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;

class ControllerTest extends TestCase {

    public function testApply () {

        $context = $this->getMockActionContext();
        $called = false;

        $action = $this->getMockAction();
        $action->method('getModule')->willReturn('module');
        $action->method('getName')->willReturn('name');
        ApplicationContext::getInstance()->addAction($action);
        $action->method('process')->will($this->returnCallback(function (ActionContext $ctx) use (&$context, &$called) {

            $this->assertSame($context, $ctx);
            $called = true;

        }));

        (new Controller('name', 'module'))->apply($context);

        $this->assertTrue($called);

    }

} 