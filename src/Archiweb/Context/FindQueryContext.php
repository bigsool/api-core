<?php


namespace Archiweb\Context;


use Archiweb\Field;
use Archiweb\Filter\Filter;

class FindQueryContext implements QueryContext {

    /**
     * @var ApplicationContext
     */
    protected $applicationContext;

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var Field[]
     */
    protected $fields;

    /**
     * @var Filter[]
     */
    protected $filters;

    /**
     * @param ApplicationContext $ctx
     * @param                    $entity
     * @param Field[]            $fields
     * @param Filter[]           $filters
     */
    public function __construct (ApplicationContext $ctx, $entity, array $fields = [], array $filters = []) {

        $this->applicationContext = $ctx;
        $this->entity = $entity;
        $this->fields = $fields;
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
     * @return Field[]
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
     * @param Field $field
     */
    public function addField (Field $field) {

        $this->fields[] = $field;

    }

    /**
     * @param Filter $filter
     */
    public function addFilter (Filter $filter) {

        $this->filters[] = $filter;

    }

}