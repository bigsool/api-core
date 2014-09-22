<?php


namespace Archiweb\Expression;


use Archiweb\Context\QueryContext;
use Archiweb\Operator\Operator;
use Archiweb\Registry;

class BinaryExpression implements ExpressionWithOperator {

    /**
     * @var Operator
     */
    protected $operator;

    /**
     * @var Expression
     */
    protected $left;

    /**
     * @var Expression
     */
    protected $right;

    /**
     * @param Operator   $operator
     * @param Expression $left
     * @param Expression $right
     */
    public function __construct (Operator $operator, Expression $left, Expression $right) {

        $this->operator = $operator;
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * @param Registry     $registry
     * @param QueryContext $context
     *
     * @return string
     */
    public function resolve (Registry $registry, QueryContext $context) {

        $leftStr = $this->getLeft()->resolve($registry, $context);
        $rightStr = $this->getRight()->resolve($registry, $context);

        return $leftStr . ' ' . $this->getOperator()->toDQL($rightStr);
    }

    /**
     * @return Expression
     */
    public function getLeft () {

        return $this->left;
    }

    /**
     * @return Expression
     */
    public function getRight () {

        return $this->right;
    }

    /**
     * @return Operator
     */
    public function getOperator () {

        return $this->operator;
    }
}