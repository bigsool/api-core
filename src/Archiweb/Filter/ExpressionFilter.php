<?php

namespace Archiweb\Filter;

use Archiweb\Expression\ExpressionWithOperator as ExpressionWithOperator;

class ExpressionFilter extends Filter {

    private $command;

    /**
     * @param string $entity
     * @param string $name
     * @param string $command
     * @param ExpressionWithOperator $expression
     */
    function __construct ($entity,$name,$command, ExpressionWithOperator $expression) {

        parent::__construct($entity,$name,$expression);
        $this->command = $command;

    }

}
