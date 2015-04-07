<?php


namespace Core\Context;


use Core\Field\KeyPath;
use Core\Filter\Filter;
use Symfony\Component\Yaml\Exception\RuntimeException;

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
     * @var RequestContext
     */
    protected $reqCtx;

    /**
     * @var string[]
     */
    protected $rights;

    /**
     * @param string         $entity
     * @param RequestContext $reqCtx
     * @param string[]       $rights
     */
    public function __construct ($entity, RequestContext $reqCtx = NULL, array $rights = []) {

        if (!is_string($entity)) {
            throw new RuntimeException('$entity must be a string');
        }

        if (!is_array($rights)) {
            throw new RuntimeException('$rights must be a array');
        }

        $this->entity = $entity;
        $this->rights = $rights;

        if (is_null($reqCtx)) {
            $reqCtx = new RequestContext();
        }
        $this->reqCtx = $reqCtx;
    }

    /**
     * @return array
     */
    public function getRights () {

        return $this->rights;
    }

    /**
     * @return RequestContext
     */
    public function getReqCtx () {

        return $this->reqCtx;
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
     * @param mixed $value
     */
    public function setParam ($key, $value) {

        $this->params[$key] = $value;

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