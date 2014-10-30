<?php


namespace Archiweb\Context;


use Archiweb\Field\KeyPath;
use Archiweb\Filter\Filter;

class FindQueryContext implements QueryContext {

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string[]
     */
    protected $joinedEntities = [];

    /**
     * @var KeyPath[]
     */
    protected $keyPaths = [];

    /**
     * @var Filter[]
     */
    protected $filters = [];

    /**
     * @param $entity
     */
    public function __construct ($entity) {

        $this->entity = $entity;

    }

    /**
     * @return string
     */
    public function getEntity () {

        return $this->entity;

    }

    /**
     * @return string[]
     */
    public function getJoinedEntities () {

        return $this->joinedEntities;

    }

    /**
     * @return KeyPath[]
     */
    public function getKeyPaths () {

        return $this->keyPaths;

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return $this->filters;

    }

    /**
     * @param KeyPath $keyPath
     * @param string  $alias
     */
    public function addKeyPath (KeyPath $keyPath, $alias = NULL) {

        $keyPath->setAlias($alias);

        $this->keyPaths[] = $keyPath;

    }

    /**
     * @param Filter $filter
     */
    public function addFilter (Filter $filter) {

        $this->filters[] = $filter;

    }

    /**
     * @return array
     */
    public function getParams () {

        return $this->params;

    }

    /**
     * @param array $params
     */
    public function setParams (array $params) {

        $this->params = $params;

    }

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function getParam ($key) {

        return isset($this->params[$key]) ? $this->params[$key] : NULL;

    }

    /**
     * @param string $entity
     */
    public function addJoinedEntity ($entity) {

        $this->joinedEntities[] = $entity;

    }

}