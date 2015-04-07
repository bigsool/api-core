<?php


namespace Core\Module\Student;


use Core\Validation\AbstractConstraintsProvider;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\String;

class Validation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'schoolName' => [
                new String(),
                new Length(['max' => 255]),
            ],
            'number' => [
                new String(),
                new Length(['max' => 255]),
            ],
        ];

    }
}