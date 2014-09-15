<?php


namespace Archiweb\Operator;


class OrOperatorTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testToDQL()
    {
        $operator = new OrOperator();
        $this->assertEquals('OR', $operator->toDQL());
    }

} 