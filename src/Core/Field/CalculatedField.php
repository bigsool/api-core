<?php


namespace Core\Field;


use Core\Context\FindQueryContext;
use Core\Registry;

class CalculatedField extends RelativeField {

    /**
     * @var array[][]
     */
    protected static $calculatedFields = [];

    /**
     * @var bool
     */
    protected $shouldThrowExceptionIfFieldNotFound = true;

    /**
     * @var null|string
     */
    protected $_value;

    /**
     * @param string   $entity
     * @param string   $field
     * @param callable $function
     * @param array    $requiredFields
     */
    public static function create ($entity, $field, callable $function, array $requiredFields = []) {

        static::$calculatedFields[$entity][$field] = [$function, $requiredFields];

    }

    /**
     * return string
     */
    public function _getValue () {

        return $this->_value ?: $this->getValue();

    }

    /**
     * @param Registry         $registry
     * @param FindQueryContext $ctx
     *
     * @return string[]
     */
    public function resolve (Registry $registry, FindQueryContext $ctx) {

        $this->shouldThrowExceptionIfFieldNotFound = false;
        $this->process($ctx);
        $this->shouldThrowExceptionIfFieldNotFound = true;

        $entity = $this->resolvedEntity;
        $field = $this->getValue();

        if (!isset(static::$calculatedFields[$entity][$field])) {
            throw new \RuntimeException("Calculated field {$entity}.{$field} not found");
        }

        list(, $requiredFields) = static::$calculatedFields[$this->resolvedEntity][$this->getValue()];

        $fields = [];
        $pos = strrpos($this->getValue(), '.');
        $prefix = '';
        if ($pos !== false) {
            $prefix = substr($this->getValue(), 0, $pos + 1);
        }

        foreach ($requiredFields as $requiredField) {

            $relativeField = new RelativeField($prefix . $requiredField);
            $fields = array_merge($fields, $relativeField->resolve($registry, $ctx));

        }

        return $fields;

    }

    /**
     * @return bool
     */
    public function shouldThrowExceptionIfFieldNotFound () {

        return $this->shouldThrowExceptionIfFieldNotFound;

    }

}