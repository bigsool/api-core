<?php

namespace Core\Validation;

use Core\Validation\Parameter\Constraint;

interface ConstraintsProvider {

    /**
     * @param string $field
     * @param bool   $makeOptional
     * @return Constraint[]
     */
    public function getConstraintsFor (string $field, bool $makeOptional = FALSE);

    /**
     * @return Constraint[][]
     */
    public function getConstraintsList ();

}