<?php


namespace Core\Expression;


use Core\Context\QueryContext;
use Core\Operator\Operator;
use Core\Registry;

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

    /**
     * @param Registry     $registry
     * @param QueryContext $context
     *
     * @return string
     */
    public function resolve (Registry $registry, QueryContext $context) {

        return
            '(' . array_reduce($this->getExpressions(), function ($prev, Expression $expr) use ($registry, $context) {

                if ($prev) {
                    $prev = $prev . ' ' . $this->getOperator()->toDQL() . ' ';
                }

                return $prev . $expr->resolve($registry, $context);
            }) . ')';

    }

    /**
     *
     */
    public function __clone()
    {
        $clonedExpression = [];
        foreach ($this->expressions as $expression) {
            $clonedExpression = clone $expression;
        }
        $this->expressions = $clonedExpression;
    }
}