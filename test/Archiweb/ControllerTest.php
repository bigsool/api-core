<?php


namespace Archiweb;


use Archiweb\Context\ActionContext;

class ControllerTest extends TestCase {

    public function testApply () {

        $context = $this->getMockActionContext();
        $called = false;

        $action = $this->getMockAction();
        $action->method('process')->will($this->returnCallback(function (ActionContext $ctx) use (&$context, &$called) {

            $this->assertSame($context, $ctx);
            $called = true;

        }));

        (new Controller($action))->apply($context);

        $this->assertTrue($called);

    }

} 