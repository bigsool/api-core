<?php

namespace Core\Module\CompanyFeature;

use Core\Validation\AbstractConstraintsProvider;
use Symfony\Component\Validator\Constraints as Assert;

class CompanyValidation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'name' =>
                [
                    new Assert\NotBlank(),
                ]
            ,
            'id'   =>
                [
                    new Assert\NotBlank(),
                ]
            ,
        ];

    }
}