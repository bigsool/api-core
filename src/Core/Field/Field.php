<?php


namespace Core\Field;


use Core\Context\FindQueryContext;
use Core\Registry;

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
     * @param string $entity
     * @param string $name
     */
    public function __construct ($entity, $name) {

        if (!is_string($entity) || !is_string($name)) {
            throw new \RuntimeException('invalid type');
        }

        $this->entity = $entity;
        $this->name = $name;

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

} 