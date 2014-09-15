<?php

namespace Archiweb\Filter;

abstract class Filter {

    private $expression;
    private $name;
    private $entity;

    function __construct ($entity,$name,$expression) {

        $this->expression = $expression;
        $this->name = $name;
        $this->entity = $entity;

    }

    public function getExpression() {

        return $this->expression;

    }

    public function getName() {

        return $this->name;

    }

    public function getEntity() {

        return $this->entity;

    }

}
