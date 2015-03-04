<?php


namespace Core\Module\User;

use Core\Validation\AbstractConstraintsProvider;
use Symfony\Component\Validator\Constraints as Assert;

class UserValidation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'firstName'    =>
                [
                    new Assert\Type(['type'=>'string']),
                    new Assert\Length(['max'=>255]),
                ]
            ,
            'lastName' =>
                [
                    new Assert\Type(['type'=>'string']),
                    new Assert\Length(['max'=>255]),
                ]
            ,
        ];

    }
}