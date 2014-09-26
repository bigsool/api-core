<?php

namespace Archiweb\Filter;

use Archiweb\Expression\BinaryExpression;
use Archiweb\Expression\Value;
use Archiweb\Operator\EqualOperator;
use Archiweb\Operator\GreaterOrEqualOperator;
use Archiweb\Operator\NotEqualOperator;
use Archiweb\Operator\LowerOrEqualOperator;
use Archiweb\Operator\LowerThanOperator;
use Archiweb\Operator\GreaterThanOperator;

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
        else if (strpos($expression, '>=')) {
            $strOperator = '>=';
            $operator = new GreaterOrEqualOperator();
        }
        else if (strpos($expression, '<=')) {
            $strOperator = '<=';
            $operator = new LowerOrEqualOperator();
        }
        else if (strpos($expression, '<')) {
            $strOperator = '<';
            $operator = new LowerThanOperator();
        }
        else if (strpos($expression, '>')) {
            $strOperator = '>';
            $operator = new GreaterThanOperator();
        }
        else if (strpos($expression, '=')) {
                $strOperator = '=';
                $operator = new EqualOperator();
        }


        $operandes = explode($strOperator, $expression);

        $binaryExpression = new BinaryExpression($operator, new Value($operandes[0]), new Value($operandes[1]));

        return $binaryExpression;

    }

}
