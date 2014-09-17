<?php

namespace Archiweb\Filter;

use Archiweb\Expression\Expression;

abstract class Filter {

    private $expression;
    private $name;
    private $entity;

    /**
     * @param string $entity
     * @param string $name
     * @param Expression $expression
     */
    function __construct ($entity,$name,Expression $expression = null) {

        $this->expression = $expression;
        $this->name = $name;
        $this->entity = $entity;

    }

    /**
     * @return Expression
     */
    public function getExpression() {

        return $this->expression;

    }

    /**
     * @return string
     */
    public function getName() {

        return $this->name;

    }

    /**
     * @return string
     */
    public function getEntity() {

        return $this->entity;

    }

}
