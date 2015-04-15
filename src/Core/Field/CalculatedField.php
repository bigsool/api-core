<?php


namespace Core\Field;


use Core\Context\FindQueryContext;
use Core\Expression\AbstractKeyPath;
use Core\Expression\Resolver;
use Core\Registry;

class CalculatedField implements ResolvableField {

    use Resolver {
        Resolver::resolve as protected _resolve;
    }

    /**
     * @var array[][]
     */
    protected static $calculatedFields = [];

    /**
     * @var bool
     */
    protected $shouldThrowExceptionIfFieldNotFound = true;

    /**
     * @var string|void
     */
    protected $alias;

    /**
     * @var string
     */
    protected $value;

    /**
     * @param string $value
     */
    public function __construct ($value) {

        if (!AbstractKeyPath::isValidKeyPath($value)) {
            throw new \RuntimeException('invalid KeyPath');
        }

        $this->value = $value;

    }

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
     * @return string
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
     * @param ResolvableField $field
     *
     * @return bool
     */
    public function isEqual (ResolvableField $field) {

        return $field instanceof self && $this->resolvedEntity == $field->resolvedEntity
               && $this->resolvedField == $field->resolvedField;

    }

    /**
     * @param Registry         $registry
     * @param FindQueryContext $ctx
     *
     * @return string[]
     */
    public function resolve (Registry $registry, FindQueryContext $ctx) {

        $this->getFinalFields($registry, $ctx);

        return [];

    }

    /**
     * @param Registry         $registry
     * @param FindQueryContext $ctx
     *
     * @return ResolvableField[]
     */
    public function getFinalFields (Registry $registry, FindQueryContext $ctx) {

        $this->shouldThrowExceptionIfFieldNotFound = false;
        $this->process($ctx);
        $this->shouldThrowExceptionIfFieldNotFound = true;

        $entity = $this->resolvedEntity;
        $field = $this->getValue();

        if (!isset(static::$calculatedFields[$entity][$field])) {
            throw new \RuntimeException("Calculated field {$entity}.{$field} not found");
        }

        list(, $requiredFields) = static::$calculatedFields[$entity][$field];

        $fields = [];

        foreach ($requiredFields as $requiredField) {

            $fields[] = new RealField($requiredField);

        }

        return $fields;

    }

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function exec (array &$data) {

        $entity = $this->resolvedEntity;
        $field = $this->getValue();

        if (!isset(static::$calculatedFields[$entity][$field])) {
            throw new \RuntimeException("Calculated field {$entity}.{$field} not found");
        }

        list($callable, $requiredFields) = static::$calculatedFields[$entity][$field];

        // Call $callable only with requiredFields
        // TODO: handle fields like company.name
        // TODO: handle alias ?
        return $data[$field] = call_user_func_array($callable, array_intersect_key($data, array_flip($requiredFields)));

    }

    /**
     * @return bool
     */
    public function shouldResolveForAWhere () {

        return false;

    }

    /**
     * @return bool
     */
    public function shouldThrowExceptionIfFieldNotFound () {

        return $this->shouldThrowExceptionIfFieldNotFound;

    }

    /**
     * @return string
     */
    public function getResolvedField () {

        return $this->resolvedField;

    }

    /**
     * @return string
     */
    public function getResolvedEntity () {

        return $this->resolvedEntity;

    }
}