<?php


namespace Core\Expression;

use Core\TestCase;

class NAryExpressionTest extends TestCase {

    /**
     *
     */
    public function testGetExpressions () {

        $operator = $this->getMock('\Core\Operator\Operator');

        $tester = function ($params) use ($operator) {

            $exp = new NAryExpression($operator, $params);
            $this->assertEquals($params, $exp->getExpressions());
        };

        $tester([]);

        $tester([$this->getMock('\Core\Expression\Expression')]);

        $tester([
                    $this->getMock('\Core\Expression\Expression'),
                    $this->getMock('\Core\Expression\Expression'),
                    $this->getMock('\Core\Expression\Expression')
                ]);
    }

    /**
     *
     */
    public function testGetOperator () {

        $operator = $this->getMock('\Core\Operator\Operator');

        $exp = new NAryExpression($operator, []);

        $this->assertEquals($operator, $exp->getOperator());

    }

    /**
     *
     */
    public function testResolve () {

        $registry = $this->getMockRegistry();
        $context = $this->getMockQueryContext();

        $operator = $this->getMock('\Core\Operator\CompareOperator');
        $operator->method('toDQL')->willReturn('AND');

        $expr1 = $this->getMock('\Core\Expression\Expression');
        $expr1->method('resolve')->willReturn('Expr. 1');

        $expr2 = $this->getMock('\Core\Expression\Expression');
        $expr2->method('resolve')->willReturn('Expr. 2');

        $expr3 = $this->getMock('\Core\Expression\Expression');
        $expr3->method('resolve')->willReturn('Expr. 3');

        $expr = new NAryExpression($operator, []);
        $this->assertEquals('()', $expr->resolve($registry, $context));

        $expr = new NAryExpression($operator, [$expr1]);
        $this->assertEquals('(Expr. 1)', $expr->resolve($registry, $context));

        $expr = new NAryExpression($operator, [$expr1, $expr2, $expr3]);
        $this->assertEquals('(Expr. 1 AND Expr. 2 AND Expr. 3)', $expr->resolve($registry, $context));
    }

} 