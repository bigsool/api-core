<?php


namespace Archiweb\Expression;


use Archiweb\Context;
use Archiweb\Operator\Operator;
use Archiweb\Registry;

class BinaryExpression implements ExpressionWithOperator
{

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
     * @param Operator $operator
     * @param Expression $left
     * @param Expression $right
     */
    public function __construct(Operator $operator, Expression $left, Expression $right)
    {
        $this->operator = $operator;
        $this->left = $left;
        $this->right = $right;
    }

    /**
     * @return Expression
     */
    public function getLeft()
    {
        return $this->left;
    }

    /**
     * @return Expression
     */
    public function getRight()
    {
        return $this->right;
    }

    /**
     * @param Registry $registry
     * @param Context $context
     * @return string
     */
    public function resolve(Registry $registry, Context $context)
    {
        $leftStr = $this->getLeft()->resolve($registry, $context);
        $rightStr = $this->getRight()->resolve($registry, $context);

        return $leftStr . ' ' . $this->getOperator()->toDQL($rightStr);
    }

    /**
     * @return Operator
     */
    public function getOperator()
    {
        return $this->operator;
    }
}