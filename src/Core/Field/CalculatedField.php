<?php


namespace Core\Field;


use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Expression\AbstractKeyPath;
use Core\Expression\Resolver;
use Core\Registry;
use Core\Util\ArrayExtra;
use Core\Util\ModelConverter;

class CalculatedField implements ResolvableField {

    use Resolver {
        Resolver::resolve as protected _resolve;
    }

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
     * @var boolean
     */
    protected $useLeftJoin;

    /**
     * @var callable
     */
    protected $function;

    /**
     * @var array
     */
    protected $requiredFields;

    /**
     * @param callable $function
     * @param array    $requiredFields
     * @param bool     $useLeftJoin
     */
    public function __construct (callable $function, array $requiredFields = [],
                                 $useLeftJoin = false) {

        $this->function = $function;
        $this->requiredFields = $requiredFields;
        $this->useLeftJoin = $useLeftJoin;

    }

    /**
     * @param mixed $model
     *
     * @return mixed
     */
    public function execute (&$model) {

        $data = (new ModelConverter(ApplicationContext::getInstance()))->toArray($model, $this->requiredFields);

        $params = [];
        foreach ($this->requiredFields as $requiredField) {
            $params[] = ArrayExtra::magicalGet($data, $requiredField);
        }

        // Call $callable only with requiredFields
        // TODO: handle alias ?
        return call_user_func_array($this->function, $params);

    }

    /**
     * @param boolean $useLeftJoin
     */
    public function setUseLeftJoin ($useLeftJoin) {

        $this->useLeftJoin = !!$useLeftJoin;
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
    public function getResolvedEntity () {

        return $this->resolvedEntity;

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
    public function getValue () {

        return $this->value;

    }

    /**
     * @param string $value
     */
    public function setValue ($value) {

        if (!AbstractKeyPath::isValidKeyPath($value)) {
            throw new \RuntimeException('invalid KeyPath');
        }

        $this->value = $value;
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

        $fields = [];

        foreach ($this->requiredFields as $requiredField) {

            $fields[] = $field = new RealField($requiredField);
            $field->setUseLeftJoin($this->useLeftJoin);

        }

        return $fields;

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
     * @return bool
     */
    public function shouldUseLeftJoin () {

        return $this->useLeftJoin;

    }

}