<?php


namespace Core\Validation\Parameter;

use Symfony\Component\Validator\Constraints;

class PositiveInteger extends Constraint {

    public function __construct () {

        $this->setConstraint(new Constraints\Type(['type' => 'int']));
        $this->setConstraint(new Constraints\GreaterThanOrEqual(0));
        $this->setErrorCode(ERROR_INVALID_PARAM_INT);

    }

}
