<?php


namespace Archiweb\Operator;


class GreaterOrEqualOperatorTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testToDQL () {

        $operator = new GreaterOrEqualOperator();
        $this->assertEquals('>= qwe', $operator->toDQL('qwe'));
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidFormat () {

        $operator = new GreaterOrEqualOperator();
        $operator->toDQL();
    }

} 