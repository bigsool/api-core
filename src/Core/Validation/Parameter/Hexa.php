<?php


namespace Core\Validation\Parameter;


use Symfony\Component\Validator\Constraints\Regex as SfRegex;

class Hexa extends Constraint {

    public function __construct () {

        $this->setConstraint(new SfRegex(['pattern' => '/^[0-9a-f]*$/i']));
        $this->setErrorCode(ERROR_INVALID_PARAM_HEXA);

    }
}