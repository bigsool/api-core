<?php


namespace Core\Operator;


class NotEqualOperatorTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testToDQL () {

        $operator = new NotEqualOperator();
        $this->assertEquals('!= qwe', $operator->toDQL('qwe'));
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidFormat () {

        $operator = new NotEqualOperator();
        $operator->toDQL();
    }

} 