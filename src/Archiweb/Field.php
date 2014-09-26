<?php


namespace Archiweb;


use Archiweb\Rule\Rule;

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
     * @var Rule[]
     */
    protected $rules = [];

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

    /**
     * @return void|Rule
     */
    public function getRules () {

        return $this->rules;

    }

    public function addRule (Rule $rule) {

        $this->rules[] = $rule;

    }

} 