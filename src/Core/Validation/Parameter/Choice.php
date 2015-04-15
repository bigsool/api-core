<?php


namespace Core\Validation\Parameter;

use Symfony\Component\Validator\Constraints;

class Choice extends Constraint {

    /**
     * @param array $options
     */
    public function __construct (array $options = []) {

        $this->setConstraint(new Constraints\Choice($options));
        $this->setErrorCode(ERROR_INVALID_PARAM_CHOICE);

    }

}