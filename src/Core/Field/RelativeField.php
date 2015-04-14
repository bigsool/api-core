<?php


namespace Core\Field;


use Core\Context\QueryContext;
use Core\Expression\AbstractKeyPath;
use Core\Expression\Resolver;
use Core\Registry;

class RelativeField {

    use Resolver {
        Resolver::resolve as _resolve;
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
     * @param Registry     $registry
     * @param QueryContext $ctx
     *
     * @return string[]
     */
    public function resolve(Registry $registry, QueryContext $ctx) {

        return [$this->_resolve($registry, $ctx)];

    }

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
     * @param $keyPath
     *
     * @return bool
     */
    public function isEqual ($keyPath) {

        return ($keyPath instanceof self)
               && ($keyPath->resolvedEntity === $this->resolvedEntity)
               && ($keyPath->resolvedField === $this->resolvedField)
               && ($keyPath->getValue() === $this->getValue());

    }

    /**
     * @return bool
     */
    public function shouldResolveForAWhere () {

        return false;

    }

    /**
     * @return string
     */
    public function getValue () {

        return $this->value;

    }

    /**
     * @return bool
     */
    public function isAggregate () {

        return is_a($this, 'Core\Field\Aggregate');

    }

    /**
     * @return bool
     */
    protected function isUsedInExpression () {

        return false;

    }
}