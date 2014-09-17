<?php

namespace Archiweb;


class ActionContextTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testParams () {

        $ctx = new Context();

        $array = ['a', 'b' => 2, ['c']];

        $ctx->setParams($array);

        $actionCtx = new ActionContext($ctx);

        $this->assertEquals($array, $actionCtx->getParams());
        $this->assertEquals($array[0], $actionCtx->getParam(0));
        $this->assertArrayHasKey($array['b'], $actionCtx->getParam('b'));

    }

    /**
     *
     */
    public function testImplementArrayAccess () {

        $ctx = new ActionContext(new Context());

        $this->assertInstanceOf('\ArrayAccess', $ctx);

        $array = ['a', 'b' => 2, ['c']];

        foreach ($array as $key => $value) {
            $ctx[$key] = $value;
        }


        foreach ($array as $key => $value) {
            $this->assertArrayHasKey($key, $ctx);
            $this->assertEquals($value, $ctx[$key]);
            unset($ctx[$key]);
            $this->assertArrayNotHasKey($key, $ctx);
        }

    }

} 