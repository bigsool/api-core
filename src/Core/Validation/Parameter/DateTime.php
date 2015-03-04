<?php


namespace Core\Validation\Parameter;

use Symfony\Component\Validator\Constraints;

class DateTime extends Constraint {

    public function __construct () {

        $this->setConstraint(new Constraints\DateTime());
        $this->setErrorCode(ERROR_INVALID_PARAM_DATETIME);

    }

}