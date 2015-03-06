<?php


namespace Core\Module\Credential;


use Core\Context\ActionContext;
use Core\Context\FindQueryContext;
use Core\Field\Aggregate;
use Core\Field\KeyPath;
use Core\Filter\StringFilter;
use Core\Model\Company;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    public function login (ActionContext $actionContext, array $params) {

        $registry = ApplicationContext::getInstance()->getNewRegistry();

        $qryCtx = new FindQueryContext('TestUser');

        $qryCtx->addKeyPath(new Aggregate('count',['*']),'valid');

        $qryCtx->addFilter(new StringFilter('Credential','bla','email = :email AND password = :password'));

        $qryCtx->setParams(['email' => $params['email'], 'password' => $params['password']]);

        $actionContext['credential'] = $registry->find($qryCtx, true);

    }

}