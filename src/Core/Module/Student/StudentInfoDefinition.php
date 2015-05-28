<?php


namespace Core\Module\Student;


use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\String;
use Symfony\Component\Validator\Constraints\Length;

class StudentInfoDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'StudentInfo';

    }

    /**
     * @param array $params
     *
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraints (array &$params) {

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