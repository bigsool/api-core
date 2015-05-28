<?php


namespace Core\Module\User;


use Core\Filter\Filter;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Choice;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\NotNull;
use Core\Validation\Parameter\String;

class UserDefinition extends ModuleEntityDefinition {

    /**
     * @param array $params
     *
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraints (array &$params) {

        return [
            'firstName' =>
                [
                    new String(),
                    new Length(['max' => 255]),
                    new NotNull(),
                ]
            ,
            'lastName'  =>
                [
                    new String(),
                    new Length(['max' => 255]),
                    new NotNull(),
                ]
            ,
            'lang'      =>
                [
                    new Choice(['choices' => ['fr', 'en']]),
                    new NotBlank(),
                ]
            ,
        ];

    }

    /**
     * @return string
     */
    public function getEntityName () {

        return 'User';

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return [new StringFilter('User', 'userForId', 'id = :id')];

    }

    /**
     * @return callable
     */
    public function getPreModifyCallback () {

        return function (array &$params, $isCreation) {

            if ($isCreation) {
                $params['creationDate'] = new \DateTime;
            }
            elseif (array_key_exists('creationDate', $params)) {
                unset($params['creationDate']);
            }

        };

    }

}