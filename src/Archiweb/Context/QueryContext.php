<?php


namespace Archiweb\Context;


use Archiweb\Field;
use Archiweb\Filter\Filter;
use Archiweb\Operation;

class QueryContext implements ApplicationContextProvider {

    /**
     * @var Filter[]
     */
    protected $filters = array();

    /**
     * @var Field[]
     */
    protected $fields = array();

    /**
     * @var array
     */
    protected $params = array();

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $command;

    /**
     * @var ApplicationContext
     */
    protected $applicationContext;

    /**
     * @param ApplicationContext $context
     */
    public function __construct (ApplicationContext $context) {

        $this->applicationContext = $context;

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

    /**.
     * @return Filter[]
     */
    public function getFilters () {

        return $this->filters;

    }

    /**
     * @param Filter $filter
     */
    public function addFilter (Filter $filter) {

        if (!in_array($filter, $this->filters, true)) {
            $this->filters[] = $filter;
        }

    }

    /**.
     * @return Field[]
     */
    public function getFields () {

        return $this->fields;

    }

    /**
     * @param Field $field
     */
    public function addField (Field $field) {

        if (!in_array($field, $this->fields, true)) {
            $this->fields[] = $field;
        }

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
     * @return string
     */
    public function getCommand () {

        return $this->command;

    }

    /**
     * @param string $command
     */
    public function setCommand ($command) {

        $this->command = $command;

    }

    /**
     * @return ApplicationContext
     */
    public function getApplicationContext () {

        return $this->applicationContext;

    }
}