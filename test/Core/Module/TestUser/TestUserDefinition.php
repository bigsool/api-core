<?php


namespace Core\Module\TestUser;


use Core\Field\Aggregate;
use Core\Field\Calculated;
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

    /**
     * @return Calculated[]
     */
    public function getFields () {

        return [
            'lastLoginDate' => new Aggregate('MAX','credential.loginHistories.date')
        ];

    }

}