<?php


namespace Archiweb\Expression;


use Archiweb\Context;
use Archiweb\Operator\Operator;
use Archiweb\Registry;

class BinaryExpression implements ExpressionWithOperator
{

    /**
     * @param Operator $operator
     * @param Expression $left
     * @param Expression $right
     */
    public function __construct(Operator $operator, Expression $left, Expression $right)
    {
        // TODO: Implement constructor
    }

    /**
     * @return Expression
     */
    public function getLeft()
    {
        // TODO: Implement getLeft() method
    }

    /**
     * @return Expression
     */
    public function getRight()
    {
        // TODO: Implement getRight() method
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