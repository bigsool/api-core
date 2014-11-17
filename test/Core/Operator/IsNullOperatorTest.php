<?php


namespace Core\Operator;


class IsNullOperatorTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testToDQL () {

        $operator = new IsNullOperator();
        $this->assertEquals('IS NULL', $operator->toDQL());
    }

} 