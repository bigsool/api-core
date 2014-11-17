<?php


namespace Core\Expression;


use Core\Context\QueryContext;
use Core\Registry;

class Value implements Expression {

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param mixed $value
     */
    public function __construct ($value) {

        if (!is_scalar($value)) {
            throw new \RuntimeException("Value can only be a scalar, got " . gettype($value));
        }

        $this->value = $value;

    }

    /**
     * @return Expression[]
     */
    public function getExpressions () {

        return [];

    }

    /**
     * @param Registry     $registry
     * @param QueryContext $context
     *
     * @return string
     */
    public function resolve (Registry $registry, QueryContext $context) {

        $v = $this->getValue();
        if (is_string($v)) {
            return "'$v'";
        }

        return (string)$v;

    }

    /**
     * @return mixed
     */
    public function getValue () {

        return $this->value;

    }
}