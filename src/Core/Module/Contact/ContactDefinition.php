<?php


namespace Core\Module\Contact;


use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Email;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\String;

class ContactDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'Contact';

    }

    /**
     * @param array $params
     *
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList (array &$params) {

        return [
            'label'    => [
                new String(),
                new Length(['max' => 255]),
            ],
            'streets'  => [
                new String(),
                new Length(['max' => 65535]),
            ],
            'city'     => [
                new String(),
                new Length(['max' => 255]),
            ],
            'state'    => [
                new String(),
                new Length(['max' => 255]),
            ],
            'zip'      => [
                new String(),
                new Length(['max' => 255]),
            ],
            'country'  => [
                new String(),
                new Length(['max' => 255]),
            ],
            'mobile'   => [
                new String(),
                new Length(['max' => 255]),
            ],
            'landLine' => [
                new String(),
                new Length(['max' => 255]),
            ],
            'email'    => [
                new Email(),
            ],
        ];

    }
}