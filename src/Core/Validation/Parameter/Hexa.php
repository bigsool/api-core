<?php


namespace Core\Validation\Parameter;


use Symfony\Component\Validator\Constraints\Regex;

class Hexa extends Constraint {

    public function __construct () {

        $this->setConstraint(new Regex(['pattern' => '/^[0-9a-f]+$/']));
        $this->setErrorCode(ERROR_INVALID_PARAM_HEXA);

    }
}