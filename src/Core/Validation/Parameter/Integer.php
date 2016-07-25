<?php


namespace Core\Validation\Parameter;

use Symfony\Component\Validator\Constraints;

class Integer extends Constraint {

    public function __construct () {

        $this->setConstraint(new Constraints\Type(['type' => 'int']));
        $this->setErrorCode(ERROR_INVALID_PARAM_INT);

    }

}