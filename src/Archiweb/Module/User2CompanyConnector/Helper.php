<?php


namespace Archiweb\Module\User2CompanyConnector;


use Archiweb\Context\ActionContext;
use Archiweb\Context\FindQueryContext;
use Archiweb\Model\Company;
use Archiweb\Parameter\Parameter;
use Archiweb\Field\KeyPath as FieldKeyPath;

class Helper {

    /**
     * @param ActionContext $actCtx
     * @param Parameter[]   $params
     */
    public function listUsers (ActionContext $actCtx, array $params) {

        $appCtx = $actCtx->getApplicationContext();
        $registry = $actCtx->getApplicationContext()->getNewRegistry();

        $qryCtx = new FindQueryContext($appCtx, 'User');

        $qryCtx->addKeyPath(new FieldKeyPath('*'));
        $qryCtx->addKeyPath(new FieldKeyPath('company'));

        $qryCtx->setParams(['authUser' => $actCtx['authUser']]);

        $result = $registry->find($qryCtx, false);

        $actCtx['users'] = $result;

    }



} 