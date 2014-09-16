<?php


namespace Archiweb\Expression;


use Archiweb\Context;
use Archiweb\Registry;

class Parameter extends Value
{

    /**
     * @param string $value
     * @throws \RuntimeException
     */
    public function __construct($value)
    {
        if (!is_string($value)) {
            throw new \RuntimeException('invalid type');
        }
        if (!preg_match('/^:[a-zA-Z_0-9-]+$/', $value)) {
            throw new \RuntimeException('invalid format');
        }
        parent::__construct($value);
    }

    /**
     * @param Registry $registry
     * @param Context $context
     * @return string
     */
    public function resolve(Registry $registry, Context $context)
    {
        // TODO: Implement the resolve() method
    }

} 