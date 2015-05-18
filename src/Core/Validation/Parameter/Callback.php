<?php


namespace Core\Validation\Parameter;

use Symfony\Component\Validator\Constraints;

class Callback extends Constraint {

    public function __construct (callable $callback, $errorCode = NULL) {

        $this->setConstraint(new Constraints\Callback($callback));
        $this->setErrorCode(ERROR_INVALID_PARAM);

    }

}