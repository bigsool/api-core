<?php


namespace Core\Context;


use Core\Parameter\SafeParameter;
use Core\Parameter\UnsafeParameter;
use Core\TestCase;

class ActionContextTest extends TestCase {

    public function testRequestContext () {

        $auth = $this->getMockAuth();
        $params = ['a' => 0, 'b', new \stdClass()];
        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn($params);
        $reqCtx->method('getAuth')->willReturn($auth);
        $ctx = new ActionContext($reqCtx);

        $this->assertSame($reqCtx, $ctx->getParentContext());
        $this->assertSame($reqCtx, $ctx->getRequestContext());
        $this->assertInternalType('array', $ctx->getParams());
        $this->assertCount(count($params), $ctx->getParams());
        $this->assertSame($auth, $ctx->getAuth());
        foreach ($ctx->getParams() as $key => $value) {
            $this->assertArrayHasKey($key, $params);
            $this->assertInstanceOf('\Core\Parameter\UnsafeParameter', $value);
            $this->assertSame($params[$key], $value->getValue());
        }

    }

    public function testActionContext () {

        $auth = $this->getMockAuth();
        $params = ['a' => new UnsafeParameter(0), new SafeParameter('b'), new UnsafeParameter(new \stdClass())];
        $actCtx = $this->getMockActionContext();
        $actCtx->method('getParams')->willReturn($params);
        $actCtx->method('getAuth')->willReturn($auth);
        $ctx = new ActionContext($actCtx);

        $this->assertSame($actCtx, $ctx->getParentContext());
        $this->assertSame($params, $ctx->getParams());
        $this->assertSame($auth, $ctx->getAuth());

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

        $ctx->setParams([]);
        $this->assertEmpty($ctx->getParams());

    }

    /**
     * @expectedException \PHPUnit_Framework_Error
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

        $ctx->setParams([]);
        $this->assertEmpty($ctx->getVerifiedParams());

        $ctx->setVerifiedParam('qwe', $qweParam);

        $ctx->setVerifiedParams([]);
        $this->assertEmpty($ctx->getVerifiedParams());

    }

    /**
     * @expectedException \PHPUnit_Framework_Error
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

    /**
     * @expectedException \PHPUnit_Framework_Error_Notice
     */
    public function testNoticeArrayObject () {

        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn([]);
        $ctx = new ActionContext($reqCtx);
        $childCtx = new ActionContext($ctx);

        $this->assertNull($childCtx['qwe']);

    }

    public function testArrayObject () {

        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn([]);
        $ctx = new ActionContext($reqCtx);
        $childCtx = new ActionContext($ctx);

        $ctx['qwe'] = 'mother';
        $this->assertSame('mother', $childCtx['qwe']);
        $this->assertSame('mother', $ctx['qwe']);

        $childCtx['qwe'] = 'child';
        $this->assertSame('child', $childCtx['qwe']);
        $this->assertSame('mother', $ctx['qwe']);

    }

} 