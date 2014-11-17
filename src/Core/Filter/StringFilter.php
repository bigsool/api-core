<?php

namespace Core\Filter;

use Core\Expression\BinaryExpression;
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
    function __construct ($entity, $name, $expression) {

        parent::__construct($entity, $name, $this->stringToExpression($expression));

    }

    function stringToExpression ($expression) {

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

        $operandes = explode($strOperator, $expression);

        $binaryExpression =
            new BinaryExpression($operator, $this->getExpressionFromString(trim($operandes[0])),
                                 $this->getExpressionFromString(trim($operandes[1])));

        return $binaryExpression;

    }

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
