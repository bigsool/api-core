<?php

namespace Archiweb\Context;


use Archiweb\Parameter\Parameter;
use Archiweb\TestCase;

class RequestContextTest extends TestCase {

    /**
     *
     */
    public function testParams () {

        $ctx = new RequestContext($this->getApplicationContext());

        $array = [$this->getParameterMock('a'), 'b' => $this->getParameterMock(2), $this->getParameterMock(['c'])];

        $ctx->setParams($array);

        $this->assertSame($array, $ctx->getParams());
        $this->assertSame($array[0], $ctx->getParam(0));
        $this->assertSame($array['b'], $ctx->getParam('b'));

    }

    /**
     * @param $value
     *
     * @return Parameter
     */
    protected function getParameterMock ($value) {

        $mock = $this->getMockBuilder('\Archiweb\Parameter\Parameter')
                     ->disableOriginalConstructor()
                     ->getMock();
        $mock->method('getValue')->willReturn($value);

        return $mock;

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidParameterType () {

        (new RequestContext($this->getApplicationContext()))->setParams(['key' => 'value']);

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidParametersType () {

        (new RequestContext($this->getApplicationContext()))->setParams('value');

    }

} 