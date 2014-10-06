<?php


namespace Archiweb\Expression;


use Archiweb\Operator\AndOperator;
use Archiweb\Operator\EqualOperator;
use Archiweb\TestCase;

class BinaryExpressionTest extends TestCase {

    /**
     *
     */
    public function testGetOperator () {

        $operator = $this->getMock('\Archiweb\Operator\CompareOperator');

        $exp = new BinaryExpression(
            $operator,
            $this->getMockExpression(),
            $this->getMockExpression()
        );

        $this->assertEquals($operator, $exp->getOperator());

    }

    /**
     *
     */
    public function testGetLeft () {

        $operator = $this->getMock('\Archiweb\Operator\CompareOperator');
        $left = $this->getMockExpression();
        $right = $this->getMockExpression();

        $exp = new BinaryExpression($operator, $left, $right);

        $this->assertEquals($left, $exp->getLeft());

    }

    /**
     *
     */
    public function testGetRight () {

        $operator = $this->getMock('\Archiweb\Operator\CompareOperator');
        $left = $this->getMockExpression();
        $right = $this->getMockExpression();

        $exp = new BinaryExpression($operator, $left, $right);

        $this->assertEquals($right, $exp->getRight());

    }

    /**
     *
     */
    public function testResolveWithMocks () {

        $registry = $this->getMockRegistry();
        $context = $this->getMockQueryContext();

        $operator = $this->getMock('\Archiweb\Operator\CompareOperator');
        $operator->method('toDQL')->will($this->returnCallback(function ($v) {

            return "= $v";

        }));

        $left = $this->getMockExpression();
        $left->method('resolve')->willReturn('I\'m left');

        $right = $this->getMockExpression();
        $right->method('resolve')->willReturn('I\'m right');

        $exp = new BinaryExpression($operator, $left, $right);

        $this->assertEquals("(I'm left = I'm right)", $exp->resolve($registry, $context));
    }

    public function testResolveQweEquals1 () {

        $registry = $this->getMockRegistry();
        $context = $this->getMockQueryContext();


        $operator = new EqualOperator();

        $left = new Value("Qwe");
        $right = new Value(1);

        $exp = new BinaryExpression($operator, $left, $right);

        $this->assertEquals("('Qwe' = 1)", $exp->resolve($registry, $context));
    }

    public function testResolveAAndB () {

        $registry = $this->getMockRegistry();
        $context = $this->getMockQueryContext();


        $operator = new AndOperator();

        $left = new Value("A");
        $right = new Value("B");

        $exp = new BinaryExpression($operator, $left, $right);

        $this->assertEquals("('A' AND 'B')", $exp->resolve($registry, $context));
    }

} 