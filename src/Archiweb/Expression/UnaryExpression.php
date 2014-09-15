<?php


namespace Archiweb\Expression;


class UnaryExpression implements ExpressionWithOperator
{

    /**
     * @param $compareOperator
     * @param $value
     */
    public function __construct($compareOperator, $value)
    {
        // TODO: Implement constructor
    }

    /**
     * @return Archiweb\Value
     */
    public function getValue()
    {
        // TODO: Implement getValue() method
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