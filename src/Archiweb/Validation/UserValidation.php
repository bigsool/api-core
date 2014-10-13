<?php


namespace Archiweb\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;

class UserValidation extends ConstraintsProvider {

    /**
     * @return Constraint[]
     */
    protected function listConstraints () {

        return [
            'email'    =>
                [
                    new Assert\NotBlank(),
                    new Assert\Email(),
                ]
            ,
            'password' =>
                [
                    new Assert\NotBlank(),
                ]
            ,
        ];

    }
}