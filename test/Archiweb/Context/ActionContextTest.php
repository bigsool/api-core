<?php


namespace Archiweb\Context;


use Archiweb\Parameter\SafeParameter;
use Archiweb\Parameter\UnsafeParameter;
use Archiweb\TestCase;

class ActionContextTest extends TestCase {

    public function testRequestContext () {

        $params = ['a' => 0, 'b', new \stdClass()];
        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn($params);
        $ctx = new ActionContext($reqCtx);

        $this->assertSame($reqCtx, $ctx->getParentContext());
        $this->assertInternalType('array', $ctx->getParams());
        $this->assertCount(count($params), $ctx->getParams());
        foreach ($ctx->getParams() as $key => $value) {
            $this->assertArrayHasKey($key, $params);
            $this->assertInstanceOf('\Archiweb\Parameter\UnsafeParameter', $value);
            $this->assertSame($params[$key], $value->getValue());
        }

    }

    public function testActionContext () {

        $params = ['a' => new UnsafeParameter(0), new SafeParameter('b'), new UnsafeParameter(new \stdClass())];
        $actCtx = $this->getMockActionContext();
        $actCtx->method('getParams')->willReturn($params);
        $ctx = new ActionContext($actCtx);

        $this->assertSame($actCtx, $ctx->getParentContext());
        $this->assertSame($params, $ctx->getParams());

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidContext () {

        new ActionContext(new ApplicationContext());

    }

    public function testParams () {

        $array = ['a' => new UnsafeParameter(0), new SafeParameter('b'), new UnsafeParameter(new \stdClass())];
        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn([]);
        $ctx = new ActionContext($reqCtx);
        $ctx->setParams($array);

        $this->assertSame($array, $ctx->getParams());
        $this->assertSame($array[0], $ctx->getParam(0));
        $this->assertSame($array['a'], $ctx->getParam('a'));

    }

    /**
     * @expectedException \Exception
     */
    public function testParamInvalidType () {

        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn([]);
        $ctx = new ActionContext($reqCtx);
        $ctx->setParams(['qwe']);

    }

    public function testGetApplicationContext () {

        $ctx = $this->getActionContext();
        $appCtx = $ctx->getApplicationContext();

        $this->assertInstanceOf('\Archiweb\Context\ApplicationContext', $appCtx);

    }

} 