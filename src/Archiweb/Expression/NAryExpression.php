<?php


namespace Archiweb\Expression;


class NAryExpression implements ExpressionWithOperator
{

    /**
     * @param $operator
     * @param [Expression] $expressions
     */
    public function __construct($operator, array $expressions)
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