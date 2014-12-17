<?php


namespace Core\Validation;

use Core\Validation\Constraints\Dictionary;
use Symfony\Component\Validator\Constraints as Assert;

class User2CompanyValidation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'user'    =>
                [
                    new Dictionary(),
                    new Assert\NotBlank()
                ]
            ,
            'company' =>
                [
                    new Dictionary(),
                    new Assert\NotBlank()
                ]
            ,
        ];

    }
}