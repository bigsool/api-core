<?php


namespace Core\Module\Company;


use Core\Validation\AbstractConstraintsProvider;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\String;

class Validation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'name' =>
                [
                    new String(),
                    new Length(['max' => 255]),
                    new NotBlank(),
                ]
            ,
            'vat'  =>
                [
                    new String(),
                    new Length(['max' => 255]),
                ]
            ,
        ];

    }
}