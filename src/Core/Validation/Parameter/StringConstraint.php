<?php


namespace Core\Validation\Parameter;

use Symfony\Component\Validator\Constraints;

// String is a reserved name
class StringConstraint extends Constraint {

    public function __construct () {

        $this->setConstraint(new Constraints\Type(['type' => 'string']));
        $this->setErrorCode(ERROR_INVALID_PARAM_STRING);

    }

}