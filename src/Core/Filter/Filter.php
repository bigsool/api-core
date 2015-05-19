<?php

namespace Core\Filter;

use Core\Expression\AbstractKeyPath;
use Core\Expression\Expression;
use Core\Expression\Parameter;
use Core\Util\ArrayExtra;

abstract class Filter {

    /**
     * @var string|NULL
     */
    protected $aliasForEntityToUse;

    /**
     * @var mixed[]
     */
    protected $params = [];

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
     * @return mixed[]
     */
    public function getParams () {

        return $this->params;

    }

    /**
     * @param mixed[] $params
     */
    public function setParams (array $params) {

        $this->params = $params;

    }

    /**
     * @return NULL|string
     */
    public function getAliasForEntityToUse () {

        return $this->aliasForEntityToUse;

    }

    /**
     * @param string|NULL $alias
     */
    public function setAliasForEntityToUse ($alias) {

        $this->aliasForEntityToUse = $alias;
        $this->setAliasForEntityToUseToThisExpressions($alias, [$this->getExpression()]);

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

            if (!($expression instanceof Expression)) {
                $qwe = 'qwde';
            }

            $this->setAliasForEntityToUseToThisExpressions($alias, $expression->getExpressions());

        }

    }

    /**
     * @return Expression
     */
    public function getExpression () {

        if ($this->params) {
            $expressions = [$this->expression];
            $isAssociative = ArrayExtra::isAssociative($this->params);
            $i = 0;
            $this->setParamToExpression($expressions, $isAssociative, $i);
        }

        return $this->expression;

    }

    /**
     * @param Expression[] $expressions
     * @param bool         $isAssociative
     * @param int          $i
     */
    protected function setParamToExpression (array $expressions, &$isAssociative, &$i) {

        foreach ($expressions as $expression) {
            if ($expression instanceof Parameter) {
                if ($isAssociative) {
                    $paramName = substr($expression->getValue(), 1);
                }
                else {
                    $paramName = $i++;
                }
                if (!array_key_exists($paramName, $this->params)) {
                    throw new \RuntimeException('Parameter not found');
                }
                $value = $this->params[$paramName];
                $expression->setParameterValue($value);
            }
            else {
                $this->setParamToExpression($expression->getExpressions(), $isAssociative, $i);
            }
        }

    }

    /**
     * @return string
     */
    public function getName () {

        return $this->name;

    }

}
