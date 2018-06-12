<?php

namespace Core\Module\Client;


use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;

class DeviceCompanyStateDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName() {
        return 'DeviceCompanyState';
    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList() {
        return [];
    }

    public function getFilters() {
        return [
            new StringFilter('DeviceCompanyState', 'DeviceCompanyStateForDevice', 'device_id = :device_id')
        ];
    }

}
