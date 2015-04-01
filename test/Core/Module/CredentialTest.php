<?php


namespace Core\Module;

use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\RequestContext;
use Core\TestCase;

class CredentialTest extends TestCase {

    public function testLogin () {

        // TODO: write the test, this one cannot work, Credential doesn't exists in this context
        /*
        $appCtx = ApplicationContext::getInstance();
        $appCtx->setProduct('Archipad');

        $credentialModuleManager = new Credential\ModuleManager();
        $credentialModuleManager->loadActions($appCtx);
        $credentialModuleManager->loadHelpers($appCtx);

        $loginAction = $appCtx->getAction('Core\Credential','login');

        $actionContext = new ActionContext(new RequestContext());
        $actionContext->setParams([
             'login'     => 'thierry@bigsool.com',
             'password'  => 'qweqwe'
        ]);

        $loginAction->process($actionContext);
         */

    }

}