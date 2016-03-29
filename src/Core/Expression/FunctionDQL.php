<?php

namespace Core\Expression;

use Core\Context\QueryContext;
use Core\Registry;


class FunctionDQL implements Expression {

    /**
     * @var string
     */
    protected $dqlFunction;

    /**
     * @var Array
     */
    protected $args;

    /**
     * FunctionDQL constructor.
     * @param string $dqlFunction
     * @param Array $args
     */
    public function __construct ($dqlFunction, $args) {

        $this->dqlFunction = $dqlFunction;
        $this->args = $args;

    }

    /**
     * @return Array
     */
    public function getExpressions () {

        return $this->args;

    }

    /**
     * @param Registry $registry
     * @param QueryContext $context
     * @return string
     */
    public function resolve (Registry $registry, QueryContext $context) {

        $values = [];
        foreach ($this->args as $arg) {
            $values[] = $arg->resolve($registry, $context);
        }

        return '(' . $this->dqlFunction . ' (' . implode(',',$values) . '))';

    }

}