<?php


namespace Core\Module\TestUser;


use Core\Module\ModuleEntityDefinition;

class TestUserDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

       return 'TestUser';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        return [];

    }
}