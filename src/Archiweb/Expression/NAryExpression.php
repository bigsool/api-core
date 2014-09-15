<?php


namespace Archiweb\Expression;


use Archiweb\Context;
use Archiweb\Operator\Operator;
use Archiweb\Registry;

class NAryExpression implements ExpressionWithOperator
{

    /**
     * @param Operator $operator
     * @param Expression[] $expressions
     */
    public function __construct(Operator $operator, array $expressions)
    {
        // TODO: Implement constructor
    }

    /**
     * @return Expression[]
     */
    public function getExpressions()
    {
        // TODO: Implement getExpressions
    }

    /**
     * @param Registry $registry
     * @param Context $context
     * @return string
     */
    public function resolve(Registry $registry, Context $context)
    {
        // TODO: Implement resolve() method.
    }

    /**
     * @return Operator
     */
    public function getOperator()
    {
        // TODO: Implement getOperator() method.
    }
}