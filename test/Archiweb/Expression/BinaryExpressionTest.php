<?php


namespace Archiweb\Expression;


class BinaryExpressionTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testGetOperator()
    {

        $operator = $this->getMock('\Archiweb\Operator\CompareOperator');

        $exp = new BinaryExpression(
            $operator,
            $this->getMock('\Archiweb\Expression\Expression'),
            $this->getMock('\Archiweb\Expression\Expression')
        );

        $this->assertEquals($operator, $exp->getOperator());

    }

    /**
     *
     */
    public function testGetLeft()
    {

        $operator = $this->getMock('\Archiweb\Operator\CompareOperator');
        $left = $this->getMock('\Archiweb\Expression\Expression');
        $right = $this->getMock('\Archiweb\Expression\Expression');

        $exp = new BinaryExpression($operator, $left, $right);

        $this->assertEquals($left, $exp->getLeft());

    }

    /**
     *
     */
    public function testGetRight()
    {

        $operator = $this->getMock('\Archiweb\Operator\CompareOperator');
        $left = $this->getMock('\Archiweb\Expression\Expression');
        $right = $this->getMock('\Archiweb\Expression\Expression');

        $exp = new BinaryExpression($operator, $left, $right);

        $this->assertEquals($right, $exp->getRight());

    }

    /**
     *
     */
    public function testResolve()
    {
        $registry = $this->getMock('\Archiweb\Registry');
        $context = $this->getMock('\Archiweb\Context');

        $operator = $this->getMock('\Archiweb\Operator\CompareOperator');
        $operator->method('toDQL')->will($this->returnCallback(function ($v) {
            return "= $v";
        }));

        $left = $this->getMock('\Archiweb\Expression\Expression');
        $left->method('resolve')->willReturn('I\'m left');

        $right = $this->getMock('\Archiweb\Expression\Expression');
        $right->method('resolve')->willReturn('I\'m right');

        $exp = new BinaryExpression($operator, $left, $right);

        $this->assertEquals("I'm left = I'm right", $exp->resolve($registry, $context));
    }

} 