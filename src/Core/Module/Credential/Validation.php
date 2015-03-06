<?php


namespace Core\Module\Credential;


use Core\Validation\AbstractConstraintsProvider;
use Core\Validation\Parameter\String;
use Core\Validation\Parameter\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class Validation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'email' =>
                [
                    new Email(),
                ]
            ,
            'password'  =>
                [
                    new String(),
                    new Length(['max' => 255]),
                    new NotBlank(),
                ]
            ,
        ];

    }
}