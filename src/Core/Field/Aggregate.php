<?php

namespace Core\Field;

use Core\Context\FindQueryContext;
use Core\Registry;

class Aggregate implements Calculated {

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
     * @var string
     */
    protected $base;

    /**
     * @var string
     */
    protected $fieldName;

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
        /**
         * @var $fieldsToUse ResolvableField[]
         */
        $fieldsToUse = [];
        foreach ($this->args as $arg) {
            $relativeField = new RelativeField(($this->getBase() ? $this->getBase() . '.' : '') . $arg);
            $resolvableFieldsFromRelativeField = $relativeField->resolve($registry, $ctx);
            $fieldsToUse[] = end($resolvableFieldsFromRelativeField);
        }
        foreach ($fieldsToUse as $fieldToUse) {
            $values .= implode(',', $fieldToUse->resolve($registry, $ctx)) . ',';
        }
        $values = substr($values, 0, strlen($values) - 1);

        $entity = $ctx->getEntity();
        if (!$this->getAlias()) {
            $this->setAlias(($this->getBase() ? $this->getBase() . '_' : '') . $this->getFieldName());
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

    /**
     * @return string
     */
    public function getBase () {

        return $this->base;

    }

    /**
     * @param string $base
     */
    public function setBase ($base) {

        $this->base = $base;

    }

    /**
     * @return string
     */
    public function getFieldName () {

        return $this->fieldName;

    }

    /**
     * @param string $fieldName
     */
    public function setFieldName ($fieldName) {

        $this->fieldName = $fieldName;

    }



}