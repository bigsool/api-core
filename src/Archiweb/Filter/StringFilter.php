<?php

namespace Archiweb\Filter;

class StringFilter extends Filter {

    private $command;

    /**
     * @param string $entity
     * @param string $name
     * @param string $expression
     * @param string $command
     */
    function __construct ($entity, $name, $expression, $command) {

        parent::__construct($entity,$name);
        $this->command = $command;

    }

    function parseExpression ($expression) {
        $operator = null;
        if (strpos($expression,'=')) {
            $operator = '=';
        }
        else if (strpos($expression,'!=')) {
            $operator = '!=';
        }
        $result = explode($operator,$expression);

    }

}
