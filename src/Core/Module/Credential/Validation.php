<?php


namespace Core\Module\Credential;

use Core\Validation\AbstractConstraintsProvider;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\String;
use Core\Validation\Parameter\Email;
use Core\Validation\Parameter\Int;

class Validation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'login' =>
                [
                    new Email(),
                    new NotBlank(),
                ]
            ,
            'password'  =>
                [
                    new String(),
                    new Length(['max' => 255]),
                    new NotBlank(),
                ]
        ];

    }
}