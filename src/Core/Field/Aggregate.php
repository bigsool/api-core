<?php

namespace Core\Field;

use Core\Context\QueryContext;
use Core\Registry;

class Aggregate extends RelativeField {

    /**
     * @var string
     */
    protected $fn;

    /**
     * @var string
     */
    protected $args;

    /**
     * @param String    $fn
     * @param String [] $args
     */
    public function __construct ($fn, $args) {

        $this->fn = $fn;
        $this->args = $args;
        $this->value = $fn;

    }

    /**
     * @param Registry     $registry
     * @param QueryContext $ctx
     *
     * @return string
     */
    public function resolve (Registry $registry, QueryContext $ctx) {

        $values = "";
        foreach ($this->args as $arg) {
            $keyPath = new RelativeField($arg);
            $values .= $keyPath->resolve($registry, $ctx) . ',';
        }
        $values = substr($values, 0, strlen($values) - 1);

        $entity = $ctx->getEntity();
        if (!$this->getAlias()) {
            $this->setAlias($entity . ucfirst($this->value));
        }

        return $this->fn . '(' . $values . ')';

    }

}