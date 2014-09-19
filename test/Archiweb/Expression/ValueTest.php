<?php


namespace Archiweb\Expression;


use Archiweb\Context;
use Archiweb\Registry;

class ValueTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @expectedException \RuntimeException
     */
    public function testValueWithObject () {

        new Value(new \stdClass());

    }

    /**
     * @expectedException \RuntimeException
     */
    public function testValueWithArray () {

        new Value(array());

    }

    public function testValueWithString () {

        $v = "string";

        $value = new Value($v);
        $this->assertEquals($v, $value->getValue());
        $this->assertEquals('"' . $v . '"', $value->resolve($this->registry, $this->context));

    }

    public function testValueWithInt () {

        $v = 10;

        $value = new Value($v);
        $this->assertEquals($v, $value->getValue());
        $this->assertEquals($v . '', $value->resolve($this->registry, $this->context));

    }

    public function testValueWithSignedInt () {

        $v = -10;

        $value = new Value($v);
        $this->assertEquals($v, $value->getValue());
        $this->assertEquals($v . '', $value->resolve($this->registry, $this->context));

    }

    public function testValueWithFloat () {

        $v = 1.1;

        $value = new Value($v);
        $this->assertEquals($v, $value->getValue());
        $this->assertEquals($v . '', $value->resolve($this->registry, $this->context));

    }

    protected function setUp () {

        $this->registry = $this->getMockBuilder('\Archiweb\Registry')
                               ->disableOriginalConstructor()
                               ->getMock();
        $this->context = $this->getMock('\Archiweb\Context');

    }

} 