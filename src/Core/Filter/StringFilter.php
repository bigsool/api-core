<?php

namespace Core\Filter;

use Core\Expression\BinaryExpression;
use Core\Expression\Expression;
use Core\Expression\KeyPath;
use Core\Expression\Parameter;
use Core\Expression\Value;
use Core\Operator\EqualOperator;
use Core\Operator\GreaterOrEqualOperator;
use Core\Operator\GreaterThanOperator;
use Core\Operator\LowerOrEqualOperator;
use Core\Operator\LowerThanOperator;
use Core\Operator\NotEqualOperator;

class StringFilter extends Filter {

    /**
     * @param string $entity
     * @param string $name
     * @param string $expression
     */
    public function __construct ($entity, $name, $expression) {

        parent::__construct($entity, $name, $this->stringToExpression($expression));

    }

    /**
     * @param string $expression
     *
     * @return BinaryExpression
     */
    public function stringToExpression ($expression) {

        $operator = NULL;
        $strOperator = "";

        if (strpos($expression, '!=')) {
            $strOperator = '!=';
            $operator = new NotEqualOperator();
        }
        elseif (strpos($expression, '>=')) {
            $strOperator = '>=';
            $operator = new GreaterOrEqualOperator();
        }
        elseif (strpos($expression, '<=')) {
            $strOperator = '<=';
            $operator = new LowerOrEqualOperator();
        }
        elseif (strpos($expression, '<')) {
            $strOperator = '<';
            $operator = new LowerThanOperator();
        }
        elseif (strpos($expression, '>')) {
            $strOperator = '>';
            $operator = new GreaterThanOperator();
        }
        elseif (strpos($expression, '=')) {
            $strOperator = '=';
            $operator = new EqualOperator();
        }

        $operands = explode($strOperator, $expression);

        $binaryExpression =
            new BinaryExpression($operator, $this->getExpressionFromString(trim($operands[0])),
                                 $this->getExpressionFromString(trim($operands[1])));

        return $binaryExpression;

    }

    /**
     * @param string $str
     *
     * @return Expression
     */
    private function getExpressionFromString ($str) {

        $expression = NULL;

        if (Parameter::isValidParameter($str)) {
            $expression = new Parameter($str);
        }
        elseif (KeyPath::isValidKeyPath($str)) {
            $expression = new KeyPath($str);
        }
        else {
            $str = trim($str, '"');
            $str = trim($str, "'");
            $expression = new Value($str);
        }

        return $expression;

    }

}
