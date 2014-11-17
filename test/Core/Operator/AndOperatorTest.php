<?php


namespace Core\Operator;


class AndOperatorTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testToDQL () {

        $operator = new AndOperator();
        $this->assertEquals('AND', $operator->toDQL());

    }

} 