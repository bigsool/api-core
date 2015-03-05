<?php
namespace Core\Validation;

use Symfony\Component\Validator\Constraint;

interface ConstraintsProvider {

    /**
     * @param string $name
     * @param mixed  $value
     * @param bool   $forceOptional
     *
     * @return bool
     */
    public function validate ($name, $value, $forceOptional = false);

    /**
     * @param string $name
     *
     * @return Constraint[]|null
     */
    public function getConstraintsFor ($name);
}