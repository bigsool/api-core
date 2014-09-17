<?php


namespace Archiweb\Operator;


class InOperatorTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testToDQL () {

        $operator = new InOperator();
        $this->assertEquals('IN ()', $operator->toDQL());

        $operator = new InOperator();
        $this->assertEquals('IN (qwe)', $operator->toDQL('qwe'));
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidFormat () {

        $operator = new InOperator();
        $operator->toDQL(new \stdClass());
    }

} 