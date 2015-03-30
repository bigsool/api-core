<?php


namespace Core\Field;


use Core\Expression\AbstractKeyPath;

class KeyPath extends AbstractKeyPath {

    /**
     * @var string|void
     */
    protected $alias;

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
               && ($keyPath->entity === $this->entity)
               && ($keyPath->field === $this->field)
               && ($keyPath->getValue() === $this->getValue());

    }

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