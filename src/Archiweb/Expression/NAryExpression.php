<?php


namespace Archiweb\Expression;


use Archiweb\Operator\Operator;

class NAryExpression implements ExpressionWithOperator
{

    /**
     * @param Operator $operator
     * @param [Expression] $expressions
     */
    public function __construct(Operator $operator, array $expressions)
    {
        // TODO: Implement constructor
    }

    /**
     * @return [Expression]
     */
    public function getExpressions()
    {
        // TODO: Implement getExpressions
    }

    /**
     * @param Archiweb\Registry $registry
     * @param Archiweb\Context $context
     * @return string
     */
    public function resolve($registry, $context)
    {
        // TODO: Implement resolve() method.
    }

    /**
     * @return Archiweb\Operator
     */
    public function getOperator()
    {
        // TODO: Implement getOperator() method.
    }
}