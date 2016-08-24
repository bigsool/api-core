<?php

namespace Core\Validation\Parameter;


use Symfony\Component\Validator\Constraints\Regex as SfRegex;

class Bucket extends Constraint {

    /**
     * @inheritDoc
     */
    public function __construct () {

        $this->setConstraint(new SfRegex('/^[a-z0-9][a-z0-9-]{1,61}[a-z0-9]$/'));
        $this->setErrorCode(ERROR_INVALID_PARAM_BUCKET);
    }

}