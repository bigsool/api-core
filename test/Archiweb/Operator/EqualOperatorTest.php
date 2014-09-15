<?php


namespace Archiweb\Operator;


class EqualOperatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testToDQL()
    {
        $operator = new EqualOperator();
        $this->assertEquals('= qwe', $operator->toDQL('qwe'));
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidFormat()
    {
        $operator = new EqualOperator();
        $operator->toDQL();
    }

} 