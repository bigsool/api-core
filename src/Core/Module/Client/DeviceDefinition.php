<?php


namespace Core\Module\Client;


use Core\Filter\Filter;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Choice;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\String;

class DeviceDefinition extends ModuleEntityDefinition {

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        return [
            'UUID' => [
                new String(),
                new Length(['max' => 255])
            ],
            'name' => [
                new String(),
                new Length(['max' => 255]),
            ],
            'type' => [
                new Choice(['choices' => ['ipad']]),
                new NotBlank(),
            ],
        ];

    }

    /**
     * @return string
     */
    public function getEntityName () {

        return 'Device';

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return [new StringFilter('Device', 'DeviceForUUID', 'UUID = :UUID')];

    }

}