<?php


namespace Core\Module\Student;


use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\StringConstraint;

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
                new StringConstraint(),
                new Length(['max' => 255]),
            ],
            'number'     => [
                new StringConstraint(),
                new Length(['max' => 255]),
            ],
        ];

    }
}