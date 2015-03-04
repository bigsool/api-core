<?php


namespace Core\Module\Company;


use Core\Validation\AbstractConstraintsProvider;
use Core\Validation\Parameter\String;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

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