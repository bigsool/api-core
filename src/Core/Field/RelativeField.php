<?php


namespace Core\Field;


use Core\Context\FindQueryContext;
use Core\Expression\AbstractKeyPath;
use Core\Expression\Resolver;
use Core\Registry;

class RelativeField {

    use Resolver {
        Resolver::resolve as protected _resolve;
    }

    /**
     * @var string
     */
    protected $value;

    /**
     * @var string
     */
    protected $alias;

    /**
     * @param string|ResolvableField $value
     */
    public function __construct ($value) {

        if ($value instanceof ResolvableField) {
            $this->value = $value;
        }
        else {

            if (!AbstractKeyPath::isValidKeyPath($value)) {
                throw new \RuntimeException('invalid KeyPath');
            }

            $this->value = $value;
        }

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

        return $this->value instanceof ResolvableField ? $this->value->getValue() : $this->value;

    }

    /**
     * @param Registry         $registry
     * @param FindQueryContext $ctx
     *
     * @return ResolvableField[]
     */
    public function resolve (Registry $registry, FindQueryContext $ctx) {

        if ($this->value instanceof ResolvableField) {

            $this->value->setAlias($this->getAlias());

            return [$this->value];

        }

        $this->process($ctx);

        $field = new RealField($this->getValue());
        $field->setAlias($this->getAlias());

        return [$field];

    }

    /**
     * @return bool
     */
    public function shouldResolveForAWhere () {

        false;

    }

    /**
     * @return bool
     */
    public function shouldThrowExceptionIfFieldNotFound () {

        return false;

    }
}