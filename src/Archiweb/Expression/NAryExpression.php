<?php


namespace Archiweb\Expression;


use Archiweb\Context\QueryContext;
use Archiweb\Operator\Operator;
use Archiweb\Registry;

class NAryExpression implements ExpressionWithOperator {

    /**
     * @var Expression[]
     */
    protected $expressions;

    /**
     * @var Operator
     */
    protected $operator;

    /**
     * @param Operator     $operator
     * @param Expression[] $expressions
     */
    public function __construct (Operator $operator, array $expressions) {

        $this->operator = $operator;
        $this->expressions = $expressions;
    }

    /**
     * @param Registry     $registry
     * @param QueryContext $context
     *
     * @return string
     */
    public function resolve (Registry $registry, QueryContext $context) {

        return array_reduce($this->getExpressions(), function ($prev, Expression $expr) use ($registry, $context) {

            if ($prev) {
                $prev = $prev . ' ' . $this->getOperator()->toDQL() . ' ';
            }

            return $prev . $expr->resolve($registry, $context);
        });
    }

    /**
     * @return Expression[]
     */
    public function getExpressions () {

        return $this->expressions;
    }

    /**
     * @return Operator
     */
    public function getOperator () {

        return $this->operator;
    }
}