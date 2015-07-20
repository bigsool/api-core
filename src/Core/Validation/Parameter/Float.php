<?php
/**
 * Created by PhpStorm.
 * User: bigsool
 * Date: 17/07/15
 * Time: 10:51
 */

namespace Core\Validation\Parameter;


use Symfony\Component\Validator\Constraints\Type;

class Float extends Constraint {

    function __construct () {

        $this->setConstraint(new Type(['type' => 'float']));
        $this->setErrorCode(ERROR_INVALID_PARAM_FLOAT);

    }

}