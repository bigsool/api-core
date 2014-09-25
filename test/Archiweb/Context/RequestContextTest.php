<?php

namespace Archiweb\Context;


use Archiweb\Parameter\UnsafeParameter;
use Archiweb\TestCase;

class RequestContextTest extends TestCase {

    /**
     *
     */
    public function testParams () {

        $ctx = $this->getRequestContext();

        $array = ['a', 'b' => 2, ['c']];

        $ctx->setParams($array);

        $this->assertSame($array, $ctx->getParams());
        $this->assertSame($array[0], $ctx->getParam(0));
        $this->assertSame($array['b'], $ctx->getParam('b'));
        $this->assertSame(NULL, $ctx->getParam('qwe'));

    }

    public function testGetNewActionContextWithoutParameter () {

        $ctx = $this->getRequestContext();
        $actionContext = $ctx->getNewActionContext();
        $expected = new ActionContext($ctx);

        $this->assertEquals($expected, $actionContext);

    }

    public function testGetNewActionContextWithParameters () {

        $ctx = $this->getRequestContext();

        $params = ['a', 'b' => 1, ['c'], new \stdClass()];
        $expectedParams = [];
        foreach ($params as $key => $param) {
            $expectedParams[$key] = new UnsafeParameter($param);
        }

        $ctx->setParams($params);
        $actionContext = $ctx->getNewActionContext();

        $expected = new ActionContext($ctx);
        $expected->setParams($expectedParams);

        $this->assertEquals($expected, $actionContext);

    }

    public function testGetApplicationContext () {

        $ctx = $this->getRequestContext();
        $appCtx = $ctx->getApplicationContext();

        $this->assertInstanceOf('\Archiweb\Context\ApplicationContext', $appCtx);

    }

} 