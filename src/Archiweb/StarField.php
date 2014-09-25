<?php


namespace Archiweb;


class StarField extends Field {

    /**
     * @param string $entity
     * @param Rule   $rule
     */
    public function __construct ($entity, Rule $rule = NULL) {

        parent::__construct($entity, '*', $rule);

    }

}