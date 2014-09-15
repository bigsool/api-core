<?php


namespace Archiweb\Expression;


use Archiweb\Context;
use Archiweb\Registry;

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
     * @param Registry $registry
     * @param Context $context
     * @return string
     */
    public function resolve(Registry $registry, Context $context)
    {
        // TODO: Implement resolve() method.
    }
}