<?php


namespace Core\Module\Credential;


use Core\Filter\Filter;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Choice;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\Object;
use Core\Validation\Parameter\String;

class CredentialDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'Credential';

    }

    /**
     * @param array $params
     *
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList (array &$params) {

        return [
            'type'      =>
                [
                    new String(),
                    new Choice(['choices' => ['email']]),
                    new NotBlank(),
                ]
            ,
            'login'     =>
                [
                    new String(),
                    new NotBlank(),
                ]
            ,
            'password'  =>
                [
                    new String(),
                    new Length(['max' => 255]),
                    new NotBlank(),
                ]
            ,
        ];

    }

    /**
     * @return Filter[]
     */
    public function getFilters() {

        return [
            new StringFilter('Credential', 'CredentialForLogin', 'login = :login'),
            new StringFilter('Credential', 'CredentialForId', 'id = :id'),
        ];

    }



    /**
     * @return callable
     */
    public function getPreModifyCallback () {

        return function (array &$params) {

            if (array_key_exists('password', $params)) {
                $params['password'] = CredentialHelper::encryptPassword($params['password']);
            }

        };

    }

}