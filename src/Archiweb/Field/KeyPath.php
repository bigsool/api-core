<?php


namespace Archiweb\Field;


use Archiweb\Context\QueryContext;
use Archiweb\Expression\AbstractKeyPath;
use Archiweb\Registry;

class KeyPath extends AbstractKeyPath {

    protected $alias;

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

    /**
     * @return string
     */
    public function getAlias () {
        return $this->alias;
    }
}