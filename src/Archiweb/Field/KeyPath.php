<?php


namespace Archiweb\Field;


use Archiweb\Expression\AbstractKeyPath;

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
     * @return bool
     */
    protected function isUsedInExpression () {

        return false;

    }
}