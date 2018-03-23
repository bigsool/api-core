<?php


namespace Core\Expression;


use Core\Context\QueryContext;
use Core\Operator\CompareOperator;
use Core\Operator\EqualOperator;
use Core\Operator\NotEqualOperator;
use Core\Operator\Operator;
use Core\Registry;

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
     * @return Expression[]
     */
    public function getExpressions () {

        return [$this->getLeft(), $this->getRight()];

    }

    /**
     * @return Operator
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

        $leftStr = $this->getLeft()->resolve($registry, $context);
        $rightStr = $this->getRight()->resolve($registry, $context);

        // hack in order to handle "something = :param" where param is NULL
        // http://www.widecodes.com/CxVkUeUgVe/how-to-write-a-nullsafe-equals-is-not-distinct-from-in-doctrine2-dql.html
        // https://stackoverflow.com/questions/10416789/how-to-rewrite-is-distinct-from-and-is-not-distinct-from/18684859#18684859
        if ($this->getLeft() instanceof Parameter || $this->getRight() instanceof Parameter) {
            if ($this->getOperator() instanceof EqualOperator) {

                return sprintf('(NOT (%1$s <> %2$s OR %1$s IS NULL OR %2$s IS NULL) OR (%1$s IS NULL AND %2$s IS NULL))',
                               $leftStr, $rightStr);

            }
            if ($this->getOperator() instanceof NotEqualOperator) {

                return sprintf('((%1$s <> %2$s OR %1$s IS NULL OR %2$s IS NULL) AND NOT (%1$s IS NULL AND %2$s IS NULL))',
                               $leftStr, $rightStr);

            }
        }

        if ($this->getOperator() instanceof CompareOperator) {

            return '(' . $leftStr . ' ' . $this->getOperator()->toDQL($rightStr) . ')';

        }
        else {

            return '(' . $leftStr . ' ' . $this->getOperator()->toDQL() . ' ' . $rightStr . ')';

        }
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
     *
     */
    public function __clone()
    {
        $this->left = clone $this->left;
        $this->right = clone $this->right;
    }
}