<?php


namespace Core\Validation\Parameter;

use Symfony\Component\Validator\Constraints;

class NoValidation extends Constraint {

    public function __construct () {

        $this->setConstraint(new Constraints\Callback(function () {
        }));
        $this->setErrorCode(ERROR_INVALID_PARAM);

    }

}