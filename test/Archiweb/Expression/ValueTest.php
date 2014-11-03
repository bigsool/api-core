<?php


namespace Archiweb\Expression;


use Archiweb\TestCase;

class ValueTest extends TestCase {

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
        $this->assertEquals("'$v'", $value->resolve($this->getMockRegistry(), $this->getMockQueryContext()));

    }

    public function testValueWithInt () {

        $v = 10;

        $value = new Value($v);
        $this->assertEquals($v, $value->getValue());
        $this->assertEquals($v . '', $value->resolve($this->getMockRegistry(), $this->getMockQueryContext()));

    }

    public function testValueWithSignedInt () {

        $v = -10;

        $value = new Value($v);
        $this->assertEquals($v, $value->getValue());
        $this->assertEquals($v . '', $value->resolve($this->getMockRegistry(), $this->getMockQueryContext()));

    }

    public function testValueWithFloat () {

        $v = 1.1;

        $value = new Value($v);
        $this->assertEquals($v, $value->getValue());
        $this->assertEquals($v . '', $value->resolve($this->getMockRegistry(), $this->getMockQueryContext()));

    }

} 