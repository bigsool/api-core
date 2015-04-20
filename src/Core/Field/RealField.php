<?php


namespace Core\Field;


use Core\Context\FindQueryContext;
use Core\Expression\AbstractKeyPath;
use Core\Expression\Resolver;
use Core\Registry;

class RealField implements ResolvableField {

    use Resolver {
        Resolver::resolve as _resolve;
        Resolver::getValue as _getValue;
    }

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string|void
     */
    protected $alias;

    /**
     * @var boolean
     */
    protected $useLeftJoin = false;

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
     * @param boolean $useLeftJoin
     */
    public function setUseLeftJoin ($useLeftJoin) {

        $this->useLeftJoin = !!$useLeftJoin;
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
     * @return bool
     */
    public function shouldResolveForAWhere () {

        return false;

    }

    /**
     * @return boolean
     */
    public function shouldUseLeftJoin () {

        return $this->useLeftJoin;

    }

    /**
     * @return string
     */
    protected function _getValue () {

        return $this->getValue();

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

        return ($resolvableField instanceof self)
               && ($resolvableField->resolvedEntity === $this->resolvedEntity)
               && ($resolvableField->resolvedField === $this->resolvedField)
               && ($resolvableField->getValue() === $this->getValue());

    }

    /**
     * @param Registry         $registry
     * @param FindQueryContext $ctx
     *
     * @return string[]
     */
    public function resolve (Registry $registry, FindQueryContext $ctx) {

        return [$this->_resolve($registry, $ctx)];

    }

    /**
     * @return bool
     */
    protected function isUsedInExpression () {

        return false;

    }
}