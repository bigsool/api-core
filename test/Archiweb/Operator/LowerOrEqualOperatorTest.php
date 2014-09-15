<?php


namespace Archiweb\Operator;


class LowerOrEqualOperatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testToDQL()
    {
        $operator = new LowerOrEqualOperator();
        $this->assertEquals('<= qwe', $operator->toDQL('qwe'));
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidFormat()
    {
        $operator = new LowerOrEqualOperator();
        $operator->toDQL();
    }

} 