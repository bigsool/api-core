<?php


namespace Archiweb\Expression;


use Archiweb\Context\QueryContext;
use Archiweb\Operator\CompareOperator;
use Archiweb\Registry;

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
     * @param Registry     $registry
     * @param QueryContext $context
     *
     * @return string
     */
    public function resolve (Registry $registry, QueryContext $context) {

        $valueStr = $this->getValue()->resolve($registry, $context);

        return $valueStr . ' ' . $this->getOperator()->toDQL();
    }

    /**
     * @return Value
     */
    public function getValue () {

        return $this->value;
    }

    /**
     * @return CompareOperator
     */
    public function getOperator () {

        return $this->operator;
    }
}