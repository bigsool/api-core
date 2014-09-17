<?php

namespace Archiweb\Filter;

class ExpressionFilterTest extends \PHPUnit_Framework_TestCase {

    public function testGetEntity () {

        $expressionWithOperator = $this->getMock('\Archiweb\Expression\ExpressionWithOperator');
        $expressionFilter = new ExpressionFilter('project', 'myProject', 'select', $expressionWithOperator);
        $entity = $expressionFilter->getEntity();
        $this->assertEquals('project', $entity);

    }

    public function testGetName () {

        $expressionWithOperator = $this->getMock('\Archiweb\Expression\ExpressionWithOperator');
        $expressionFilter = new ExpressionFilter('project', 'myProject', 'select', $expressionWithOperator);
        $name = $expressionFilter->getName();
        $this->assertEquals('myProject', $name);

    }

    public function testGetExpression () {

        $expressionWithOperator = $this->getMock('\Archiweb\Expression\ExpressionWithOperator');
        $expressionFilter = new ExpressionFilter('project', 'myProject', 'select', $expressionWithOperator);
        $expression = $expressionFilter->getExpression();
        $this->assertEquals($expression, $expressionWithOperator);

    }

}
