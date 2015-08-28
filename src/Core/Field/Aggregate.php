<?php

namespace Core\Field;

use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Registry;
use Doctrine\Common\Inflector\Inflector;

class Aggregate implements Calculated {

    /**
     * @var ResolvableField
     */
    protected $resolvableField;

    /**
     * @var string
     */
    protected $fn;

    /**
     * @var string
     */
    protected $field;

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
     * @param String $fn
     * @param String $field
     */
    public function __construct ($fn, $field) {

        $this->fn = $fn;
        $this->field = $field;
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
               && $this->field == $resolvableField->field;

    }

    /**
     * @param Registry         $registry
     * @param FindQueryContext $ctx
     *
     * @return string[]
     */
    public function resolve (Registry $registry, FindQueryContext $ctx) {

        $relativeField = new RelativeField(($this->getBase() ? $this->getBase() . '.' : '') . $this->field);
        $resolvableFields = $relativeField->resolve($registry, $ctx);
        $this->resolvableField = end($resolvableFields);
        $value = implode(',', $this->resolvableField->resolve($registry, $ctx)); // TODO : should return only one value

        if (!$this->getAlias()) {
            $this->setAlias(($this->getBase() ? $this->getBase() . '_' : '') . $this->getFieldName());
        }

        return [$this->fn . '(' . $value . ')'];

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

    /**
     * @param mixed $model
     *
     * @return mixed
     */
    public function execute (&$model) {

        $resolvedEntity = $this->resolvableField->getResolvedEntity();
        $resolvedField = $this->resolvableField->getResolvedField();
        $metadata = ApplicationContext::getInstance()->getClassMetadata(Registry::realModelClassName($resolvedEntity));
        $type = $metadata->getTypeOfField($resolvedField);

        $getter = 'get' . Inflector::classify($this->getFieldName());
        $value = $model->$getter();

        if ($type == 'datetime') {
            return new \DateTime($value);
        }

        return $value;

    }

}