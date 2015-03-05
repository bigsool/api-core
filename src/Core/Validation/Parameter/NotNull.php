<?php


namespace Core\Validation\Parameter;

use Symfony\Component\Validator\Constraints;

class NotNull extends Constraint {

    public function __construct () {

        $this->setConstraint(new Constraints\NotNull());
        $this->setErrorCode(ERROR_INVALID_PARAM_NOT_NULL);

    }

}