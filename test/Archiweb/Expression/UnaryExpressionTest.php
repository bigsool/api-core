<?php


namespace Archiweb\Expression;


use Archiweb\TestCase;

class UnaryExpressionTest extends TestCase {

    /**
     *
     */
    public function testGetOperator () {

        $operator = $this->getMock('\Archiweb\Operator\CompareOperator');
        $value = $this->getMockBuilder('\Archiweb\Expression\Value')->disableOriginalConstructor()->getMock();

        $exp = new UnaryExpression($operator, $value);

        $this->assertEquals($operator, $exp->getOperator());

    }

    /**
     *
     */
    public function testGetValue () {

        $operator = $this->getMock('\Archiweb\Operator\CompareOperator');
        $value = $this->getMockBuilder('\Archiweb\Expression\Value')->disableOriginalConstructor()->getMock();

        $exp = new UnaryExpression($operator, $value);

        $this->assertEquals($value, $exp->getValue());

    }

    /**
     *
     */
    public function testResolve () {

        $registry = $this->getMockRegistry();
        $context = $this->getMockQueryContext();

        $operator = $this->getMock('\Archiweb\Operator\CompareOperator');
        $operator->method('toDQL')->willReturn('IS NULL');

        $value = $this->getMockBuilder('\Archiweb\Expression\Value')->disableOriginalConstructor()->getMock();
        $value->method('resolve')->willReturn('I\'m the value');

        $exp = new UnaryExpression($operator, $value);

        $this->assertEquals('I\'m the value IS NULL', $exp->resolve($registry, $context));
    }

} 