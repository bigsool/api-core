<?php


namespace Core\Module\Student;


use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\String;

class StudentInfoDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'StudentInfo';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        return [
            'schoolName' => [
                new String(),
                new Length(['max' => 255]),
            ],
            'number'     => [
                new String(),
                new Length(['max' => 255]),
            ],
        ];

    }
}