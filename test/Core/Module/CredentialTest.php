<?php


namespace Core\Module;

use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\RequestContext;
use Core\TestCase;

class CredentialTest extends TestCase {

    public function testLogin () {

        $appCtx = ApplicationContext::getInstance();
        $appCtx->setProduct('Archipad');

        $credentialModuleManager = new Credential\ModuleManager();
        $credentialModuleManager->loadActions($appCtx);
        $credentialModuleManager->loadHelpers($appCtx);

        $loginAction = $appCtx->getAction('Core\Credential','login');

        $actionContext = new ActionContext(new RequestContext());
        $actionContext->setParams([
             'email'     => 'thierry@bigsool.com',
             'password'  => 'qweqwe'
        ]);

        $loginAction->process($actionContext);

    }

}