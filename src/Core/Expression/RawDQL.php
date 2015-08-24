<?php
/**
 * Created by PhpStorm.
 * User: bigsool
 * Date: 24/07/15
 * Time: 10:48
 */

namespace Core\Expression;


use Core\Context\QueryContext;
use Core\Registry;

class RawDQL implements Expression {

    /**
     * @var mixed
     */
    protected $rawDQL;

    /**
     * @param mixed $value
     */
    public function __construct ($value) {

        if (!is_scalar($value)) {
            throw new \RuntimeException("Value can only be a scalar, got " . gettype($value));
        }

        $this->rawDQL = $value;

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

        $v = $this->getRawDQL();

        return $v;

    }

    /**
     * @return mixed
     */
    public function getRawDQL () {

        return $this->rawDQL;

    }

}