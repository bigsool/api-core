<?php


namespace Core\Validation\Parameter;

use Symfony\Component\Validator\Constraints;

class NotBlank extends Constraint {

    public function __construct () {

        $this->setConstraint(new Constraints\NotBlank());
        $this->setErrorCode(ERROR_INVALID_PARAM_NOT_NULL);

    }

}