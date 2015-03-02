<?php


namespace Core\Module\TestUser;

use Core\Validation\AbstractConstraintsProvider;
use Symfony\Component\Validator\Constraints as Assert;

class UserValidation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
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