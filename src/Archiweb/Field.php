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
     * @var Rule
     */
    protected $rule;

    /**
     * @param string $entity
     * @param string $name
     * @param Rule   $rule
     */
    public function __construct ($entity, $name, Rule $rule = NULL) {

        if (!is_string($entity) || !is_string($name)) {
            throw new \RuntimeException('invalid type');
        }

        $this->entity = $entity;
        $this->name = $name;
        $this->rule = $rule;

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
    public function getRule () {

        return $this->rule;

    }

} 