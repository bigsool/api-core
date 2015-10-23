<?php


namespace Core\Validation\Parameter;


use Symfony\Component\Validator\Constraints\Regex as SfRegex;

class Regex extends Constraint {

    /**
     * @param $options
     * @see http://symfony.com/doc/current/reference/constraints/Regex.html
     */
    public function __construct ($options) {

        $this->setConstraint(new SfRegex($options));
        $this->setErrorCode(ERROR_INVALID_PARAM_REGEX);

    }
}