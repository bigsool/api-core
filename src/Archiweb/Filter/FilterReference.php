<?php

namespace Archiweb\Filter;

class FilterReference extends Filter {

    /**
     * @param string $entity
     * @param string name
     */
    function __construct ($entity, $name) {

        parent::__construct($entity,$name,null);

    }

    public function getExpression () {

        // TODO

    }

}
