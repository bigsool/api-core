<?php


namespace Core\Context;


use Core\Parameter\UnsafeParameter;
use Core\TestCase;

class ActionContextTest extends TestCase {

    public function setUp () {

        parent::setUp();
        self::getApplicationContext();
    }

    public function testRequestContext () {
        $auth = $this->getMockAuth();
        $params = ['a' => 0, 'b', new \stdClass()];
        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn($params);
        $reqCtx->method('getAuth')->willReturn($auth);
        $ctx = $this->getActionContext($reqCtx);

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
        $appCtx = $this->getApplicationContext();
        $reqCtx = $appCtx->getRequestContextFactory()->getNewRequestContext();
        $auth = $this->getMockAuth();
        $params = ['a' => new UnsafeParameter(0, ''), 'b', new UnsafeParameter(new \stdClass(), '')];
        $reqCtx->setAuth($auth);
        $actCtx = $this->getActionContext($reqCtx);
        $actCtx->setParams($params);
        $ctx = $actCtx->newDerivedContextFor('', '');

        $this->assertSame($actCtx, $ctx->getParentContext());
        $this->assertSame($params, $ctx->getParams());
        $this->assertSame($auth, $ctx->getAuth());

    }

    public function testParams () {

        $array =
            ['a'                  => new UnsafeParameter(0, ''),
             0                    => 'b',
             1                    => new UnsafeParameter(new \stdClass(), ''),
             'first.second.third' => 'qwe'
            ];
        $expected =
            ['a'     => $array['a'],
             0       => 'b',
             1       => $array[1],
             'first' => ['second' => ['third' => 'qwe']]
            ];
        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn([]);
        $ctx = $this->getActionContext($reqCtx);
        $ctx->setParams($array);

        $this->assertSame($expected, $ctx->getParams());
        $this->assertSame($expected[0], $ctx->getParam(0));
        $this->assertSame($expected['a'], $ctx->getParam('a'));
        $this->assertSame($expected['first']['second'], $ctx->getParam('first.second'));

        $this->assertCount(2, $ctx->getParams([1, 'a']));
        $this->assertContains($expected[1], $ctx->getParams([1, 'a']));
        $this->assertContains($expected['a'], $ctx->getParams([1, 'a']));

        $this->assertNull($ctx->getParam('qwe'));
        $qweParam = $this->getMockParameter();
        $ctx->setParam('qwe', $qweParam);
        $this->assertSame($qweParam, $ctx->getParam('qwe'));
        $ctx->unsetParam('qwe');
        $this->assertNull($ctx->getParam('qwe'));

        $ctx->setParams([]);
        $this->assertEmpty($ctx->getParams());

    }

    /**
     * @expectedException \Exception
     */
    public function testSetParamInvalidType () {

        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn([]);
        $ctx = $this->getActionContext($reqCtx);
        $ctx->setParam(new \stdClass(), $this->getMockParameter());

    }

    public function testVerifiedParams () {

        $array = ['a' => 0, 'b', new \stdClass()];
        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn([]);
        $ctx = $this->getActionContext($reqCtx);
        $ctx->setParams($array);

        $this->assertSame($array, $ctx->getVerifiedParams());
        $this->assertSame($array[0], $ctx->getVerifiedParam(0));
        $this->assertSame($array['a'], $ctx->getVerifiedParam('a'));

        $this->assertCount(2, $ctx->getVerifiedParams([1, 'a']));
        $this->assertContains($array[1], $ctx->getVerifiedParams([1, 'a']));
        $this->assertContains($array['a'], $ctx->getVerifiedParams([1, 'a']));

        $this->assertNull($ctx->getVerifiedParam('qwe'));
        $qweParam = 'qwe';
        $ctx->setParam('qwe', $qweParam);
        $this->assertSame($qweParam, $ctx->getVerifiedParam('qwe'));

        $ctx->setParams([]);
        $this->assertEmpty($ctx->getVerifiedParams());

        $ctx->setParam('qwe', $qweParam);

        $ctx->clearVerifiedParams();
        $this->assertEmpty($ctx->getVerifiedParams());

    }

    /**
     * @expectedException PHPUnit_Framework_Error_Notice
     */
    public function testNoticeArrayObject () {

        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn([]);
        $ctx = $this->getActionContext($reqCtx);
        $childCtx = $ctx->newDerivedContextFor('', '');

        $this->assertNull($childCtx['qwe']);

    }

    public function testArrayObject () {

        $reqCtx = $this->getMockRequestContext();
        $reqCtx->method('getParams')->willReturn([]);
        $ctx = $this->getActionContext($reqCtx);
        $childCtx = $ctx->newDerivedContextFor('', '');

        $ctx['qwe'] = 'mother';
        $this->assertSame('mother', $childCtx['qwe']);
        $this->assertSame('mother', $ctx['qwe']);

        $childCtx['qwe'] = 'child';
        $this->assertSame('child', $childCtx['qwe']);
        $this->assertSame('mother', $ctx['qwe']);

    }

} 