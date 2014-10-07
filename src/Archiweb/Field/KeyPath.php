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
     * @param Registry     $registry
     * @param QueryContext $context
     *
     * @return string
     */
    public function resolve (Registry $registry, QueryContext $context) {

        $alias = parent::resolve($registry, $context);

        return isset($this->alias) ? $this->alias : $alias;

    }
}