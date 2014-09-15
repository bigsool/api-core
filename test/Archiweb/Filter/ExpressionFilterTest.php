<?php

namespace Archiweb\Filter;

class ExpressionFilterTest extends \PHPUnit_Framework_TestCase
{

    private $expressionFilter;
    private $expressionWithOperator;

    function __construct() {

        $this->expressionWithOperator = $this->getMock('\Archiweb\Expression\ExpressionWithOperator');
        $this->expressionFilter = new ExpressionFilter('project','myProject','select',$this->expressionWithOperator);

    }

    public function testGetEntity() {

        $entity = $this->expressionFilter->getEntity();
        $this->assertEquals('project', $entity);

    }

    public function testGetName() {

        $name = $this->expressionFilter->getName();
        $this->assertEquals('myProject', $name);

    }

    public function testGetExpression() {

        $expression = $this->expressionFilter->getExpression();
        $this->assertEquals($expression,$this->expressionWithOperator);

    }

}
