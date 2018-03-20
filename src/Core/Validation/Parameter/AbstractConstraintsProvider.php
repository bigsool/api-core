<?php


namespace Core\Validation\Parameter;


use Core\Validation\ConstraintsProvider;

abstract class AbstractConstraintsProvider implements ConstraintsProvider {

    /**
     * @param string $field
     *
     * @param bool   $makeOptional
     * @return Constraint[]
     */
    public function getConstraintsFor (string $field, bool $makeOptional = FALSE) {

        $constraints = $this->getConstraintsList();

        return isset($constraints[$field]) ? $constraints[$field] : [];

    }

}