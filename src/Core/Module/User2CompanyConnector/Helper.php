<?php


namespace Core\Module\User2CompanyConnector;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Field\KeyPath as FieldKeyPath;
use Core\Parameter\Parameter;

class Helper {

    /**
     * @param ActionContext $actCtx
     * @param Parameter[]   $params
     */
    public function listUsers (ActionContext $actCtx, array $params) {

        $appCtx = ApplicationContext::getInstance();
        $registry = $appCtx->getNewRegistry();

        $qryCtx = new FindQueryContext('User', $actCtx->getRequestContext());

        $qryCtx->addKeyPath(new FieldKeyPath('*'));
        $qryCtx->addKeyPath(new FieldKeyPath('company'));
        $qryCtx->addKeyPath(new FieldKeyPath('company'));

        $qryCtx->setParams(['authUser' => $actCtx->getAuth()->getUser()]);

        $result = $registry->find($qryCtx, false);

        $actCtx['users'] = $result;

    }

}