<?php


namespace Core\Module\Credential;


use Core\Context\ActionContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Module\ModuleEntityDefinition;

class LoginHistoryDefinition extends ModuleEntityDefinition {

    /***
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        return [];

    }

    /**
     * @return string
     */
    public function getEntityName () {

        return 'LoginHistory';

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

            $params['date'] = new \DateTime();
            $params['IP'] = $reqCtx->getIpAddress();
        }

        $upsertContext = new ModuleEntityUpsertContext($this, $entityId, $params, $actionContext);

        return $upsertContext;

    }

}