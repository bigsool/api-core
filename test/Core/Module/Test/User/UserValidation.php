<?php


namespace Core\Module\Test\User;

use Core\Validation\AbstractConstraintsProvider;
use Core\Validation\Parameter\Email;
use Core\Validation\Parameter\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class UserValidation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'email'    =>
                [
                    new Email(),
                    new NotBlank(),
                ]
            ,
            'password' =>
                [
                    new NotBlank(),
                ]
            ,
        ];

    }
}