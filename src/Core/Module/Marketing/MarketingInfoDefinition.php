<?php


namespace Core\Module\Marketing;


use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\String;

class MarketingInfoDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'MarketingInfo';

    }

    /**
     * @param array $params
     *
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraints (array &$params) {

        return [
            'knowsFrom' => [
                new String(),
                new Length(['max' => 255]),
            ],
        ];

    }

}