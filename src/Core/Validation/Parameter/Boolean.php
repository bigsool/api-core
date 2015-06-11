<?php
/**
 * Created by PhpStorm.
 * User: thierry
 * Date: 11/06/2015
 * Time: 17:15
 */

namespace Core\Validation\Parameter;

use Symfony\Component\Validator\Constraints;

class Boolean extends Constraint {

    public function __construct () {

        $this->setConstraint(new Constraints\Type(['type' => 'boolean']));
        $this->setErrorCode(ERROR_INVALID_PARAM_INT);

    }

}