<?php


namespace Archiweb\Operator;


class LowerThanOperatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testToDQL()
    {
        $operator = new LowerThanOperator();
        $this->assertEquals('< qwe', $operator->toDQL('qwe'));
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidFormat()
    {
        $operator = new LowerThanOperator();
        $operator->toDQL();
    }

} 