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

        new ActionContext(new \stdClass());

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

        $this->assertCount(2, $ctx->getParams([1, 'a']));
        $this->assertContains($array[1], $ctx->getParams([1, 'a']));
        $this->assertContains($array['a'], $ctx->getParams([1, 'a']));

        $this->assertNull($ctx->getParam('qwe'));
        $qweParam = $this->getMockParameter();
        $ctx->setParam('qwe', $qweParam);
        $this->assertSame($qweParam, $ctx->getParam('qwe'));

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

    /**
     * @expectedException \Exception
     */
    public function testSetParamInvalidType () {

        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn([]);
        $ctx = new ActionContext($reqCtx);
        $ctx->setParam(new \stdClass(), $this->getMockParameter());

    }

    public function testVerifiedParams () {

        $array = ['a' => new SafeParameter(0), new SafeParameter('b'), new SafeParameter(new \stdClass())];
        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn([]);
        $ctx = new ActionContext($reqCtx);
        $ctx->setVerifiedParams($array);

        $this->assertSame($array, $ctx->getVerifiedParams());
        $this->assertSame($array[0], $ctx->getVerifiedParam(0));
        $this->assertSame($array['a'], $ctx->getVerifiedParam('a'));

        $this->assertCount(2, $ctx->getVerifiedParams([1, 'a']));
        $this->assertContains($array[1], $ctx->getVerifiedParams([1, 'a']));
        $this->assertContains($array['a'], $ctx->getVerifiedParams([1, 'a']));

        $this->assertNull($ctx->getVerifiedParam('qwe'));
        $qweParam = new SafeParameter('qwe');
        $ctx->setVerifiedParam('qwe', $qweParam);
        $this->assertSame($qweParam, $ctx->getVerifiedParam('qwe'));

    }

    /**
     * @expectedException \Exception
     */
    public function testVerifiedParamInvalidType () {

        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn([]);
        $ctx = new ActionContext($reqCtx);
        $ctx->setVerifiedParams([new UnsafeParameter('qwe')]);

    }

    /**
     * @expectedException \Exception
     */
    public function testSetVerifiedParamInvalidType () {

        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn([]);
        $ctx = new ActionContext($reqCtx);
        $ctx->setVerifiedParam(new \stdClass(), new SafeParameter('qwe'));

    }

    public function testGetApplicationContext () {

        $ctx = $this->getActionContext();
        $appCtx = $ctx->getApplicationContext();

        $this->assertInstanceOf('\Archiweb\Context\ApplicationContext', $appCtx);

    }

} 