<?php


namespace Archiweb\Validation;

use Symfony\Component\Validator\Constraints as Assert;

class CompanyValidation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'name'    =>
                [
                    new Assert\NotBlank(),
                ]
            ,
        ];

    }
}