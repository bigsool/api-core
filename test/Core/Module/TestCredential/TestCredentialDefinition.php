<?php


namespace Core\Module\TestCredential;


use Core\Module\ModuleEntityDefinition;

class TestCredentialDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'TestCredential';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        return [];

    }
}