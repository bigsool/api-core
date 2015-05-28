<?php


namespace Core\Module\Credential;


use Core\Context\ActionContext;
use Core\Module\ModuleEntityDefinition;

class LoginHistoryDefinition extends ModuleEntityDefinition {

    /***
     * @param array $params
     *
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraints (array &$params) {

        return [];

    }

    /**
     * @return string
     */
    public function getEntityName () {

        return 'LoginHistory';

    }

    /**
     * @return callable
     */
    public function getPreModifyCallback () {

        return function (array &$params, $isCreation, ActionContext $context) {

            if ($isCreation) {
                $reqCtx = $context->getRequestContext();

                $params['date'] = new \DateTime();
                $params['clientName'] = $reqCtx->getClientName();
                $params['clientVersion'] = $reqCtx->getClientVersion();
                $params['IP'] = $reqCtx->getIpAddress();
            }

        };

    }

}