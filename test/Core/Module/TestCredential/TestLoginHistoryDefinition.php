<?php


namespace Core\Module\TestCredential;


use Core\Module\ModuleEntityDefinition;

class TestLoginHistoryDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'TestLoginHistory';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        return [];

    }
}