<?php


namespace Core\Context;


use Core\Field\RelativeField;
use Core\Filter\Filter;
use Core\Module\ModuleEntity;
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
     * @var ModuleEntity
     */
    protected $moduleEntity;

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
     * @return string
     */
    public function getEntity () {

        return $this->entity;

    }

    /**
     * @param string $entity
     */
    public function setEntity ($entity) {

        $this->entity = $entity;

    }

    /**
     * @return RelativeField[]
     */
    public function getFields () {

        return $this->fields;

    }

    /**
     * @param string[] $fields
     */
    public function setFields (array $fields) {

        $this->fields = [];

        foreach ($fields as $field) {

            $this->addField($field);

        }

    }

    /**
     * @param RelativeField|string $field
     * @param string               $alias
     */
    public function addField ($field, $alias = NULL) {

        if (is_string($field)) {
            $field = new RelativeField($field);
        }
        elseif (!($field instanceof RelativeField)) {
            throw new \RuntimeException(sprintf('$field must be a string or an instance of RelativeField, %s given',
                                                gettype($field)));
        }

        $field->setAlias($alias);

        $this->fields[] = $field;

    }

    /**
     * @param RelativeField|string $field,...
     */
    public function addFields($field) {
        foreach (func_get_args() as $field) {
            $this->addField($field);
        }
    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return $this->filters;

    }

    /**
     * @param mixed[][]|mixed[] $filters
     */
    public function setFilters (array $filters) {

        $this->filters = [];

        foreach ($filters as $filter) {

            if (!is_array($filter)) {
                $filter = [$filter];
            }

            $this->addFilter($filter[0], isset($filter[1]) ? $filter[1] : NULL);

        }

    }

    /**
     * @param Filter|string $filter
     * @param mixed[]|mixed $params
     */
    public function addFilter ($filter, $params = NULL) {

        if (is_string($filter)) {

            $filter = $this->getRequestContext()->getApplicationContext()->getFilterByName($filter);

        }
        elseif (!($filter instanceof Filter)) {
            throw new \RuntimeException(sprintf('$filter must be a string or an instance of Filter, %s given',
                                                gettype($filter)));
        }

        if (func_num_args() == 2) {
            $filter->setParams(is_array($params) ? $params : [$params]);
        }

        $this->filters[] = $filter;

    }

    /**
     * @return RequestContext
     */
    public function getRequestContext () {

        return $this->reqCtx;

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
     * @param int|\Exception $exception
     *
     * @return mixed
     */
    public function findOne ($exception = NULL) {

        $entities = $this->findAll();

        $count = count($entities);

        if ($count != 1 && $exception !== false) {

            if (is_int($exception)) {
                $appCtx = $this->getRequestContext()->getApplicationContext();
                $exception = $appCtx->getErrorManager()->getFormattedError($exception);
            }
            elseif (!($exception instanceof \Exception)) {
                $exception = new \RuntimeException('one entity was expected, ' . $count . ' fetched');
            }

            throw $exception;

        }

        return $count ? $entities[0] : NULL;

    }

    /**
     * @return array
     */
    public function findAll () {

        if (!count($this->fields)) {
            $this->addField('*');
        }

        $this->getRequestContext()->getApplicationContext()->finalizeFindQueryContext($this);

        return $this->moduleEntity->find($this);

    }

    public function setRegistry () {

    }

    public function setModuleEntity (ModuleEntity $moduleEntity) {

        $this->moduleEntity = $moduleEntity;

    }

    /**
     * @return ApplicationContext
     */
    public function getApplicationContext () {

        return $this->getRequestContext()->getApplicationContext();

    }

    /**
     * @param string $entity
     *//*
    public function addJoinedEntity ($entity) {

        $this->joinedEntities[] = $entity;

    }*/

    /**
     * @return string[]
     *//*
    protected function getJoinedEntities () {

        return $this->joinedEntities;

    }*/

}