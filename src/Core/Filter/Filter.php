<?php

namespace Core\Filter;

use Core\Expression\AbstractKeyPath;
use Core\Expression\Expression;

abstract class Filter {

    /**
     * @var Expression
     */
    private $expression;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $entity;

    /**
     * @var string|NULL
     */
    protected $aliasForEntityToUse;

    /**
     * @return NULL|string
     */
    public function getAliasForEntityToUse () {

        return $this->aliasForEntityToUse;

    }

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
     * @param string|NULL $alias
     */
    public function setAliasForEntityToUse ($alias) {

        $this->aliasForEntityToUse = $alias;
        $this->setAliasForEntityToUseToThisExpressions($alias, [$this->expression]);

    }

    /**
     * @param string|NULL  $alias
     * @param Expression[] $expressions
     */
    protected function setAliasForEntityToUseToThisExpressions ($alias, array $expressions) {

        foreach ($expressions as $expression) {

            if ($expression instanceof AbstractKeyPath) {
                $expression->setAliasForEntityToUse($alias);
            }

            $this->setAliasForEntityToUseToThisExpressions($alias, $expression->getExpressions());

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

}
