<?php


namespace Core\Module\Credential;

use Core\Validation\AbstractConstraintsProvider;
use Core\Validation\Parameter\Choice;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\Object;
use Core\Validation\Parameter\String;

class Validation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'type'      =>
                [
                    new String(),
                    new Choice(['choices' => ['email']]),
                    new NotBlank(),
                ]
            ,
            'login'     =>
                [
                    new String(),
                    new NotBlank(),
                ]
            ,
            'password'  =>
                [
                    new String(),
                    new Length(['max' => 255]),
                    new NotBlank(),
                ]
            ,
            'authToken' =>
                [
                    new Object(),
                    new NotBlank(),
                ]
        ];

    }
}