<?php

namespace Core\Validation;

use Core\Validation\Parameter\Constraint;

interface ConstraintsProvider {

    /**
     * @param string $field
     *
     * @return Constraint[]
     */
    public function getConstraintsFor ($field);

    /**
     * @return Constraint[][]
     */
    public function getConstraintsList ();

}