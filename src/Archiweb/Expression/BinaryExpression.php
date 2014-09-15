<?php


namespace Archiweb\Expression;


class BinaryExpression implements ExpressionWithOperator
{

    /**
     * @param Archiweb\Operator $operator
     * @param Expression $left
     * @param Expression $right
     */
    public function __construct($operator, $left, $right)
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