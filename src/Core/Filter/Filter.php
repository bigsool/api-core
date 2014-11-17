<?php

namespace Core\Filter;

use Core\Expression\AbstractKeyPath;
use Core\Expression\Expression;

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
     * @param Expression[] $expressions
     */
    protected function setRootEntityToKeyPaths (array $expressions) {

        foreach ($expressions as $expression) {

            if ($expression instanceof AbstractKeyPath) {
                $expression->setRootEntity($this->getEntity());
            }

            $this->setRootEntityToKeyPaths($expression->getExpressions());

        }

    }

    /**
     * @return string
     */
    public function getEntity () {

        return $this->entity;

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

}
