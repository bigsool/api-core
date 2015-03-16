<?php


namespace Core\Validation\Parameter;

use Symfony\Component\Validator\Constraints;

class Email extends Constraint {

    public function __construct () {

        $this->setConstraint(new Constraints\Email());
        $this->setErrorCode(ERROR_INVALID_PARAM_EMAIL);

    }

}