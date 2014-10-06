<?php

namespace Archiweb\Filter;

use Archiweb\Expression\AbstractKeyPath;
use Archiweb\Expression\Expression;

abstract class Filter {

    private $expression;

    private $name;

    private $entity;

    /**
     * @param string     $entity
     * @param string     $name
     * @param Expression $expression
     */
    function __construct ($entity, $name, Expression $expression = NULL) {

        $this->expression = $expression;
        $this->name = $name;
        $this->entity = $entity;

        if ($expression) {
            $this->setRootEntityToKeyPaths([$expression]);
        }

    }

    /**
     * @return Expression
     */
    public function getExpression () {

        return $this->expression;

    }

    /**
     * @return string
     */
    public function getName () {

        return $this->name;

    }

    /**
     * @return string
     */
    public function getEntity () {

        return $this->entity;

    }

    /**
     * @param Expression[] $expressions
     */
    protected function setRootEntityToKeyPaths(array $expressions) {

        foreach ($expressions as $expression) {

            if ($expression instanceof AbstractKeyPath) {
                $expression->setRootEntity($this->getEntity());
            }

            $this->setRootEntityToKeyPaths($expression->getExpressions());

        }

    }

}
