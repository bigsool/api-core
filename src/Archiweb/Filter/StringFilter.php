<?php

namespace Archiweb\Filter;

use Archiweb\Expression\BinaryExpression;
use Archiweb\Expression\KeyPath;
use Archiweb\Expression\Parameter;
use Archiweb\Expression\Value;
use Archiweb\Operator\EqualOperator;
use Archiweb\Operator\GreaterOrEqualOperator;
use Archiweb\Operator\GreaterThanOperator;
use Archiweb\Operator\LowerOrEqualOperator;
use Archiweb\Operator\LowerThanOperator;
use Archiweb\Operator\NotEqualOperator;

class StringFilter extends Filter {

    private $command;

    /**
     * @param string $entity
     * @param string $name
     * @param string $expression
     * @param string $command
     */
    function __construct ($entity, $name, $expression, $command) {

        parent::__construct($entity, $name, $this->stringToExpression($expression));
        $this->command = $command;

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
