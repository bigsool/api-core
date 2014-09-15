<?php

namespace Archiweb\Filter;

use Archiweb\Expression\ExpressionWithOperator as ExpressionWithOperator;

class ExpressionFilter extends Filter {

    private $command;

    function __construct ($entity,$name,$command, ExpressionWithOperator $expression) {

        parent::__construct($entity,$name,$expression);
        $this->command = $command;

    }

}
