<?php

namespace Core\Field;

use Core\Context\FindQueryContext;
use Core\Registry;

class Aggregate implements ResolvableField {

    /**
     * @var string
     */
    protected $fn;

    /**
     * @var string
     */
    protected $args;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string|void
     */
    protected $alias;

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
     * @return string|void
     */
    public function getAlias () {

        return $this->alias;

    }

    /**
     * @param string $alias
     */
    public function setAlias ($alias) {

        $this->alias = $alias;

    }

    /**
     * @return string
     */
    public function getValue () {

        return $this->value;

    }

    /**
     * @param ResolvableField $resolvableField
     *
     * @return bool
     */
    public function isEqual (ResolvableField $resolvableField) {

        return $resolvableField instanceof self && $this->fn == $resolvableField->fn
               && $this->args == $resolvableField->args;

    }

    /**
     * @param Registry         $registry
     * @param FindQueryContext $ctx
     *
     * @return string[]
     */
    public function resolve (Registry $registry, FindQueryContext $ctx) {

        $values = "";
        $resolvableFields = [];
        foreach ($this->args as $arg) {
            $relativeField = new RelativeField($arg);
            $resolvableFields = array_merge($resolvableFields, $relativeField->resolve($registry, $ctx));
        }
        $resolvableFields = Registry::removeDuplicatedFields($resolvableFields);
        foreach ($resolvableFields as $resolvableField) {
            $values .= implode(',', $resolvableField->resolve($registry, $ctx)) . ',';
        }
        $values = substr($values, 0, strlen($values) - 1);

        $entity = $ctx->getEntity();
        if (!$this->getAlias()) {
            $this->setAlias($entity . ucfirst($this->value));
        }

        return [$this->fn . '(' . $values . ')'];

    }

    /**
     * @return string
     */
    public function getResolvedField () {

        return $this->getValue();

    }

    /**
     * @return string
     */
    public function getResolvedEntity () {

        return NULL;

    }

}