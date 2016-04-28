<?php


namespace Core\Validation\Parameter;


use Core\Validation\ConstraintsProvider;

abstract class AbstractConstraintsProvider implements ConstraintsProvider {

    /**
     * @param string $field
     *
     * @return Constraint[]
     */
    public function getConstraintsFor ($field) {

        $constraints = $this->getConstraintsList();

        return isset($constraints[$field]) ? $constraints[$field] : [];

    }

}