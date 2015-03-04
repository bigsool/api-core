<?php


namespace Core\Validation\Parameter;

use Symfony\Component\Validator\Constraints;

class Object extends Constraint {

    public function __construct () {

        $this->setConstraint(new Constraints\Type(['type' => 'array']));
        $this->setErrorCode(ERROR_INVALID_PARAM_OBJECT);

    }

}