<?php


namespace Core\Module\Client;


use Core\Context\ActionContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\String;

class ClientDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'Client';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        return [
            'name' => [
                new String(),
                new Length(['max'=>255]),
                new NotBlank()
            ],
            'version' => [
                new String(),
                new Length(['max'=>255]),
                new NotBlank()
            ],
            // TODO : LOCALE ?
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

        if (!$entityId) {
            $reqCtx = $actionContext->getRequestContext();

            $params['name'] = $reqCtx->getClientName();
            $params['version'] = $reqCtx->getClientVersion();
        }

        $upsertContext = new ModuleEntityUpsertContext($this, $entityId, $params, $actionContext);

        return $upsertContext;

    }

}