<?php


namespace Core\Validation\Parameter;

use Symfony\Component\Validator\Constraints;

class Null extends Constraint {

    public function __construct () {

        $this->setConstraint(new Constraints\Null());
        $this->setErrorCode(ERROR_INVALID_PARAM_NULL);

    }

}