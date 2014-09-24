<?php


namespace Archiweb\Context;


use Archiweb\TestCase;

class ActionContextTest extends TestCase {

    public function testRequestContext () {

        $reqCtx = $this->getMockRequestContext();
        $ctx = new ActionContext($reqCtx);

        $this->assertSame($reqCtx, $ctx->getRequestContext());

    }

    public function testParams () {

        $array = [$this->getMockParameter('a'), 'b' => $this->getMockParameter(2), $this->getMockParameter(['c'])];

        $reqCtx = $this->getMockRequestContext();
        $ctx = new ActionContext($reqCtx);
        $ctx->setParams($array);

        $this->assertSame($array, $ctx->getParams());
        $this->assertSame($array[0], $ctx->getParam(0));
        $this->assertSame($array['b'], $ctx->getParam('b'));

    }

    /**
     * @expectedException \Exception
     */
    public function testParamInvalidType () {

        $reqCtx = $this->getMockRequestContext();
        $ctx = new ActionContext($reqCtx);
        $ctx->setParams(['qwe']);

    }

    public function testGetApplicationContext () {

        $ctx = $this->getActionContext();
        $appCtx = $ctx->getApplicationContext();

        $this->assertInstanceOf('\Archiweb\Context\ApplicationContext', $appCtx);

    }

} 