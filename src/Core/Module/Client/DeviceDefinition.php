<?php


namespace Core\Module\Client;


use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\String;

class DeviceDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'Device';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        return [
            'UDID' => [
                new String(),
                new Length(['max'=>255]),
            ],
            'name' => [
                new String(),
                new Length(['max'=>255]),
            ],
            'type' => [
                new String(),
                new Length(['max'=>255]),
            ],
        ];

    }

}