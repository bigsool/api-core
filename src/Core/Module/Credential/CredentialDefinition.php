<?php


namespace Core\Module\Credential;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Context\RequestContext;
use Core\Error\ToResolveException;
use Core\Filter\Filter;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Choice;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\StringConstraint;

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

        $factory = ApplicationContext::getInstance()->getParameterFactory();

        return [
            'type'     =>
                [
                    $factory->getParameter(StringConstraint::class),
                    $factory->getParameter(Choice::class, ['choices' => ['email']]),
                    $factory->getParameter(NotBlank::class),
                ]
            ,
            'login'    =>
                [
                    $factory->getParameter(StringConstraint::class),
                    $factory->getParameter(NotBlank::class),
                ]
            ,
            'password' =>
                [
                    $factory->getParameter(StringConstraint::class),
                    $factory->getParameter(Length::class, ['max' => 255]),
                    $factory->getParameter(NotBlank::class),
                ]
            ,
            'authType' =>
                [
                    $factory->getParameter(StringConstraint::class),
                    $factory->getParameter(NotBlank::class),
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
     * @throws ToResolveException
     */
    public function createUpsertContext (array $params, $entityId, ActionContext $actionContext) {

        $upsertContext = new ModuleEntityUpsertContext($this, $entityId, $params, $actionContext);

        if (array_key_exists('password', $params)) {
            $hash = CredentialHelper::encryptPassword($upsertContext->getValidatedParam('password'));
            $upsertContext->addParam('password', $hash);
        }

        if (!$entityId) {

            $login = $upsertContext->getValidatedParam('login');

            $internalReqCtx = RequestContext::createNewInternalRequestContext();

            $findQueryContext = new FindQueryContext('Credential', $internalReqCtx);
            $findQueryContext->addField('*');
            $findQueryContext->addFilter('CredentialForLogin', $login);

            // TODO count request directly
            if ($findQueryContext->count() != 0) {
                throw new ToResolveException(ERROR_CREDENTIAL_ALREADY_EXIST);
            }

        }

        return $upsertContext;

    }

}