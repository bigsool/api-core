<?php


namespace Archiweb\Validation;

use Archiweb\Validation\Constraints\Tab;
use Symfony\Component\Validator\Constraints as Assert;

class User2CompanyValidation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'user'    =>
                [
                    new Tab(),
                    new Assert\NotBlank()
                ]
            ,
            'company' =>
                [
                    new Tab(),
                    new Assert\NotBlank()
                ]
            ,
        ];

    }
}