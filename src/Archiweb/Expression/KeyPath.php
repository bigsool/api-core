<?php


namespace Archiweb\Expression;


use Archiweb\Context\QueryContext;
use Archiweb\Filter\Filter;
use Archiweb\Registry;

class KeyPath extends Value {

    /**
     * @param string $value
     *
     * @throws \RuntimeException
     */
    public function __construct ($value) {

        if (!is_string($value)) {
            throw new \RuntimeException('invalid type');
        }
        if (!preg_match('/^[a-zA-Z_0-9]+(\.[a-zA-Z_0-9]+)*$/', $value)) {
            throw new \RuntimeException('invalid format');
        }
        parent::__construct($value);
    }

    /**
     * @return Filter[]
     */
    public function getFilters () {
        // TODO: Implement getFilters() method
    }

    /**
     * @param Registry       $registry
     * @param QueryContext $context
     *
     * @return string
     */
    public function resolve (Registry $registry, QueryContext $context) {
        // TODO: Implement the resolve() method
    }

}