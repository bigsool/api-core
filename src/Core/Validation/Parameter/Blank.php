<?php


namespace Core\Validation\Parameter;

use Symfony\Component\Validator\Constraints;

class Blank extends Constraint {

    public function __construct () {

        $this->setConstraint(new Constraints\Blank());
        $this->setErrorCode(ERROR_INVALID_PARAM_NULL);

    }

}