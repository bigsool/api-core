<?php


namespace Core\Validation\Parameter;


use Symfony\Component\Validator\Constraints\Regex as SfRegex;

class Decimal extends Constraint {

    public function __construct () {

        $this->setConstraint(new SfRegex(['pattern' => '/^[0-9]*$/i']));
        $this->setErrorCode(ERROR_INVALID_PARAM_DECIMAL);

    }
}