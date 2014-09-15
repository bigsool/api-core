<?php


namespace Archiweb\Expression;


use Archiweb\Context;
use Archiweb\Operator\CompareOperator;
use Archiweb\Registry;

class UnaryExpression implements ExpressionWithOperator
{

    /**
     * @param CompareOperator $compareOperator
     * @param Value $value
     */
    public function __construct(CompareOperator $compareOperator, Value $value)
    {
        // TODO: Implement constructor
    }

    /**
     * @return Value
     */
    public function getValue()
    {
        // TODO: Implement getValue() method
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
     * @return CompareOperator
     */
    public function getOperator()
    {
        // TODO: Implement getOperator() method.
    }
}