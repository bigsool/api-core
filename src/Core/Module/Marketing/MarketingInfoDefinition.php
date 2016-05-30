<?php


namespace Core\Module\Marketing;


use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\StringConstraint;

class MarketingInfoDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'MarketingInfo';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        return [
            'knowsFrom' => [
                new StringConstraint(),
                new Length(['max' => 255]),
            ],
        ];

    }

}