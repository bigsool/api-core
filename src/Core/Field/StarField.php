<?php


namespace Core\Field;


class StarField extends Field {

    /**
     * @param string $entity
     */
    public function __construct ($entity) {

        parent::__construct($entity, '*');

    }

}