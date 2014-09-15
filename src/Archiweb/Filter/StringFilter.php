<?php

namespace Archiweb\Filter;

class StringFilter extends Filter {

    private $command;

    function __construct ($entity, $name, $expression, $command) {

        parent::__construct($entity,$name,$expression);
        $this->command = $command;

    }


}
