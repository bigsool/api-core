<?php

namespace Archiweb\Filter;

use Archiweb\TestCase;

class ExpressionFilterTest extends TestCase {

    public function testGetEntity () {

        $expressionWithOperator = $this->getMockExpressionWithOperator();
        $expressionFilter = new ExpressionFilter('project', 'myProject', $expressionWithOperator);
        $entity = $expressionFilter->getEntity();
        $this->assertEquals('project', $entity);

    }

    public function testGetName () {

        $expressionWithOperator = $this->getMockExpressionWithOperator();
        $expressionFilter = new ExpressionFilter('project', 'myProject', $expressionWithOperator);
        $name = $expressionFilter->getName();
        $this->assertEquals('myProject', $name);

    }

    public function testGetExpression () {

        $expressionWithOperator = $this->getMockExpressionWithOperator();
        $expressionFilter = new ExpressionFilter('project', 'myProject', $expressionWithOperator);
        $expression = $expressionFilter->getExpression();
        $this->assertEquals($expression, $expressionWithOperator);

    }

    /**
     * @expectedException \Exception
     */
    public function testGetExpressionWithoutExpression () {

        (new ExpressionFilter('project', 'myProject', NULL))->getExpression();

    }

}
