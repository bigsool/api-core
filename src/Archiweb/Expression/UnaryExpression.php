<?php


namespace Archiweb\Expression;


use Archiweb\Context;
use Archiweb\Operator\CompareOperator;
use Archiweb\Registry;

class UnaryExpression implements ExpressionWithOperator
{

    /**
     * @var CompareOperator
     */
    protected $operator;

    /**
     * @var Value
     */
    protected $value;

    /**
     * @param CompareOperator $compareOperator
     * @param Value $value
     */
    public function __construct(CompareOperator $compareOperator, Value $value)
    {
        $this->operator = $compareOperator;
        $this->value = $value;
    }

    /**
     * @return Value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param Registry $registry
     * @param Context $context
     * @return string
     */
    public function resolve(Registry $registry, Context $context)
    {
        $valueStr = $this->getValue()->resolve($registry, $context);

        return $valueStr . ' ' . $this->getOperator()->toDQL();
    }

    /**
     * @return CompareOperator
     */
    public function getOperator()
    {
        return $this->operator;
    }
}