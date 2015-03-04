<?php

namespace Core\Module\Test\Company;

use Core\Validation\AbstractConstraintsProvider;
use Core\Validation\Parameter\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyValidation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'name' =>
                [
                    new NotBlank(),
                ]
            ,
            'id'   =>
                [
                    new NotBlank(),
                ]
            ,
        ];

    }
}