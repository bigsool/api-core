<?php


namespace Archiweb\Expression;


use Archiweb\Context;
use Archiweb\Registry;

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
     * @param Registry $registry
     * @param Context  $context
     *
     * @return string
     */
    public function resolve (Registry $registry, Context $context) {

        $v = $this->getValue();
        if (is_string($v)) {
            return '"' . $v . '"';
        }

        return $v . '';   // cast to string
    }

    /**
     * @return mixed
     */
    public function getValue () {

        return $this->value;
    }
}