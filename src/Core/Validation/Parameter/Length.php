<?php


namespace Core\Validation\Parameter;

use Symfony\Component\Validator\Constraints;

class Length extends Constraint {

    public function __construct (array $options) {

        $this->setConstraint(new Constraints\Length($options));
        $this->setErrorCode(ERROR_INVALID_PARAM_LENGTH);

    }

}