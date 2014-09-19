<?php


namespace Archiweb;


use Archiweb\Filter\Filter;

class Field {

    /**
     * @var string
     */
    protected $entity;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @param string $entity
     * @param string $name
     * @param Filter $filter
     */
    public function __construct ($entity, $name, Filter $filter = NULL) {

        if (!is_string($entity) || !is_string($name)) {
            throw new \RuntimeException('invalid type');
        }

        $this->entity = $entity;
        $this->name = $name;
        $this->filter = $filter;

    }

    /**
     * @return string
     */
    public function getEntity () {

        return $this->entity;

    }

    /**
     * @return string
     */
    public function getName () {

        return $this->name;

    }

    /**
     * @return void|string
     */
    public function getFilter () {

        return $this->filter;

    }

} 