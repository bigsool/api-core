<?php


namespace Core\Context;


use Core\Field\RelativeField;
use Core\Filter\Filter;

class HighLevelFindQueryContext {

    /**
     * @var RequestContext
     */
    protected $requestContext;

    /**
     * @var Filter[]
     */
    protected $filters = [];

    /**
     * @var RelativeField[]
     */
    protected $fields = [];

    /**
     * @param RequestContext $requestContext
     */
    public function __construct (RequestContext $requestContext) {

        $this->requestContext = $requestContext;

    }

    /**
     * @return RequestContext
     */
    public function getRequestContext () {

        return $this->requestContext;

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return $this->filters;

    }

    /**
     * @param mixed[][] $filters
     */
    public function setFilters (array $filters) {

        $this->filters = [];

        foreach ($filters as $filter) {

            $this->addFilter($filter[0], isset($filter[1]) ? $filter[1] : NULL);

        }

    }

    /**
     * @param string        $filterName
     * @param mixed[]|mixed $params
     */
    public function addFilter ($filterName, $params = NULL) {

        $filter = ApplicationContext::getInstance()->getFilterByName($filterName);
        if ($params !== NULL) {
            $filter->setParams((array)$params);
        }

        $this->filters[] = $filter;

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
     * @param string $field
     */
    public function addField ($field) {

        $this->fields[] = new RelativeField($field);

    }

}