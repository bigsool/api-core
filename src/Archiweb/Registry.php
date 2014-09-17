<?php


namespace Archiweb;


class Registry {

    /**
     * @param ActionContext $context
     */
    public function __construct (ActionContext $context) {
        // TODO: Implement constructor
    }

    /**
     * @param string $entity
     * @param array  $params
     *
     * @return Object
     */
    public function create ($entity, array $params) {
        // TODO: Implement create() method
    }

    /**
     * @param string $entity
     *
     * @return Object
     */
    public function find ($entity) {
        // TODO: Implement find() method
    }

    /**
     * @param string $joinName
     *
     * @return string
     */
    public function addJoin ($joinName) {
        // TODO: Implement addJoin() method
    }

    /**
     * @param string $parameter
     * @param mixed  $value
     */
    public function setParameter ($parameter, $value) {
        // TODO: Implement setParameter() method
    }

} 