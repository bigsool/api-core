<?php


namespace Archiweb\Context;


use Archiweb\Expression\KeyPath;
use Archiweb\Field\Field;
use Archiweb\Filter\Filter;

class FindQueryContext implements QueryContext {

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var ApplicationContext
     */
    protected $applicationContext;

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
    protected $keyPaths;

    /**
     * @var Filter[]
     */
    protected $filters;

    /**
     * @param ApplicationContext $ctx
     * @param string             $entity
     * @param Field[]            $keyPaths
     * @param Filter[]           $filters
     */
    public function __construct (ApplicationContext $ctx, $entity, array $keyPaths = [], array $filters = []) {

        $this->applicationContext = $ctx;
        $this->entity = $entity;
        $this->keyPaths = $keyPaths;
        $this->filters = $filters;

    }

    /**
     * @return ApplicationContext
     */
    public function getApplicationContext () {

        return $this->applicationContext;

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
     */
    public function addKeyPath (KeyPath $keyPath) {

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