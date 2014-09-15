<?php


namespace Archiweb\Operator;


class GreaterThanOperatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testToDQL()
    {
        $operator = new GreaterThanOperator();
        $this->assertEquals('> qwe', $operator->toDQL('qwe'));
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidFormat()
    {
        $operator = new GreaterThanOperator();
        $operator->toDQL();
    }

} 