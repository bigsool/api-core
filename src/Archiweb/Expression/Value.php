<?php


namespace Archiweb\Expression;


class Value implements Expression
{

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        // TODO: Implement constructor
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        // TODO: Implement getValue() method
    }

    /**
     * @param Archiweb\Registry $registry
     * @param Archiweb\Context $context
     * @return string
     */
    public function resolve($registry, $context)
    {
        // TODO: Implement resolve() method.
    }
}