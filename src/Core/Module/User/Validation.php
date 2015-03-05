<?php


namespace Core\Module\User;

use Core\Validation\AbstractConstraintsProvider;
use Core\Validation\Parameter\Choice;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\NotNull;
use Core\Validation\Parameter\String;

class Validation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'firstName' =>
                [
                    new String(),
                    new Length(['max' => 255]),
                    new NotNull(),
                ]
            ,
            'lastName'  =>
                [
                    new String(),
                    new Length(['max' => 255]),
                    new NotNull(),
                ]
            ,
            'lang'      =>
                [
                    new Choice(['choices' => ['fr', 'en']]),
                    new NotBlank(),
                ]
            ,
        ];

    }
}