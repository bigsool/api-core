<?php


namespace Core\Context;


use Core\Field\RelativeField;
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
     * @var RelativeField[]
     */
    protected $fields = [];

    /**
     * @var Filter[]
     */
    protected $filters = [];

    /**
     * @var RequestContext
     */
    protected $reqCtx;

    /**
     * @param string         $entity
     * @param RequestContext $reqCtx
     */
    public function __construct ($entity, RequestContext $reqCtx = NULL) {

        if (!is_string($entity)) {
            throw new RuntimeException('$entity must be a string');
        }

        $this->entity = $entity;

        if (is_null($reqCtx)) {
            $reqCtx = new RequestContext();
        }
        $this->reqCtx = $reqCtx;
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
     * @return RelativeField[]
     */
    public function getFields () {

        return $this->fields;

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return $this->filters;

    }

    /**
     * @param RelativeField $field
     * @param string        $alias
     */
    public function addField (RelativeField $field, $alias = NULL) {

        $field->setAlias($alias);

        $this->fields[] = $field;

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