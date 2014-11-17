<?php


namespace Core\Expression;


use Core\Context\QueryContext;
use Core\Operator\CompareOperator;
use Core\Registry;

class UnaryExpression implements ExpressionWithOperator {

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
     * @param Value           $value
     */
    public function __construct (CompareOperator $compareOperator, Value $value) {

        $this->operator = $compareOperator;
        $this->value = $value;

    }

    /**
     * @return Expression[]
     */
    public function getExpressions () {

        return [$this->getValue()];

    }

    /**
     * @return CompareOperator
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

        $valueStr = $this->getValue()->resolve($registry, $context);

        return '(' . $valueStr . ' ' . $this->getOperator()->toDQL() . ')';

    }

    /**
     * @return Value
     */
    public function getValue () {

        return $this->value;

    }
}