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

        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue () {

        return $this->value;
    }

    /**
     * @param Registry $registry
     * @param Context  $context
     *
     * @return string
     */
    public function resolve (Registry $registry, Context $context) {
        // TODO: Implement resolve() method.
    }
}