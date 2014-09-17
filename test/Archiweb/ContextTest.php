<?php

namespace Archiweb;


class ContextTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testParams () {

        $ctx = new Context();

        $array = ['a', 'b' => 2, ['c']];

        $ctx->setParams($array);

        $this->assertEquals($array, $ctx->getParams());
        $this->assertEquals($array[0], $ctx->getParam(0));
        $this->assertArrayHasKey($array['b'], $ctx->getParam('b'));

    }

    /**
     *
     */
    public function testImplementArrayAccess () {

        $ctx = new Context();

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