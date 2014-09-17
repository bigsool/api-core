<?php


namespace Archiweb\Expression;

class NAryExpressionTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testGetExpressions () {

        $operator = $this->getMock('\Archiweb\Operator\Operator');

        $tester = function ($params) use ($operator) {

            $exp = new NAryExpression($operator, $params);
            $this->assertEquals($params, $exp->getExpressions());
        };

        $tester([]);

        $tester([$this->getMock('\Archiweb\Expression\Expression')]);

        $tester([
                    $this->getMock('\Archiweb\Expression\Expression'),
                    $this->getMock('\Archiweb\Expression\Expression'),
                    $this->getMock('\Archiweb\Expression\Expression')
                ]);
    }

    /**
     *
     */
    public function testGetOperator () {

        $operator = $this->getMock('\Archiweb\Operator\Operator');

        $exp = new NAryExpression($operator, []);

        $this->assertEquals($operator, $exp->getOperator());

    }

    /**
     *
     */
    public function testResolve () {

        $registry = $this->getMockBuilder('\Archiweb\Registry')
                         ->disableOriginalConstructor()
                         ->getMock();
        $context = $this->getMock('\Archiweb\Context');

        $operator = $this->getMock('\Archiweb\Operator\CompareOperator');
        $operator->method('toDQL')->willReturn('AND');

        $expr1 = $this->getMock('\Archiweb\Expression\Expression');
        $expr1->method('resolve')->willReturn('Expr. 1');

        $expr2 = $this->getMock('\Archiweb\Expression\Expression');
        $expr2->method('resolve')->willReturn('Expr. 2');

        $expr3 = $this->getMock('\Archiweb\Expression\Expression');
        $expr3->method('resolve')->willReturn('Expr. 3');

        $expr = new NAryExpression($operator, []);
        $this->assertEquals('', $expr->resolve($registry, $context));

        $expr = new NAryExpression($operator, [$expr1]);
        $this->assertEquals('Expr. 1', $expr->resolve($registry, $context));

        $expr = new NAryExpression($operator, [$expr1, $expr2, $expr3]);
        $this->assertEquals('Expr. 1 AND Expr. 2 AND Expr. 3', $expr->resolve($registry, $context));
    }

} 