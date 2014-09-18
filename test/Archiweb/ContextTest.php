<?php

namespace Archiweb;


use Archiweb\Parameter\Parameter;

class ContextTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testParams () {

        $ctx = new Context();

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

        (new Context())->setParams(['key' => 'value']);

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidParametersType () {

        (new Context())->setParams('value');

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