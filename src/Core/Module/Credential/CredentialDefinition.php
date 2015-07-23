<?php


namespace Core\Module\Credential;


use Core\Context\ActionContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Filter\Filter;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Choice;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\String;

class CredentialDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'Credential';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        return [
            'type'     =>
                [
                    new String(),
                    new Choice(['choices' => ['email']]),
                    new NotBlank(),
                ]
            ,
            'login'    =>
                [
                    new String(),
                    new NotBlank(),
                ]
            ,
            'password' =>
                [
                    new String(),
                    new Length(['max' => 255]),
                    new NotBlank(),
                ]
            ,
            'authType' =>
                [
                    new String(),
                    new NotBlank(),
                ]
            ,
        ];

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return [
            new StringFilter('Credential', 'CredentialForLogin', 'login = :login'),
            new StringFilter('Credential', 'CredentialForId', 'id = :id'),
        ];

    }

    /**
     * @param array         $params
     * @param int|null      $entityId
     * @param ActionContext $actionContext
     *
     * @return ModuleEntityUpsertContext
     */
    public function createUpsertContext (array $params, $entityId, ActionContext $actionContext) {

        $upsertContext = new ModuleEntityUpsertContext($this, $entityId, $params, $actionContext);

        if (array_key_exists('password', $params)) {
            $hash = CredentialHelper::encryptPassword($upsertContext->getValidatedParam('password'));
            $upsertContext->addParam('password', $hash);
        }

        return $upsertContext;

    }

}