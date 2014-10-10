<?php


namespace Archiweb;


use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Context\RequestContext;
use Archiweb\Module\ModuleManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext as SymfonyRequestContext;

class Application {

    public function run() {

        $appCtx = new ApplicationContext();

        $modules = array_map('basename',glob(__DIR__.'/Module/*', GLOB_ONLYDIR));
        foreach ($modules as $moduleName) {
            $className = "\\Archiweb\\Module\\$moduleName\\ModuleManager";
            /**
             * @var ModuleManager $moduleManager
             */
            $moduleManager = new $className;
            $moduleManager->load($appCtx);
        }

        $request = Request::createFromGlobals();
        $SfnReqCtx = new SymfonyRequestContext();
        $SfnReqCtx->fromRequest($request);

        $matcher = new UrlMatcher($appCtx->getRoutes(), $SfnReqCtx);

        $reqCtx = new RequestContext($appCtx);

        /**
         * @var Controller $controller
         */
        $controller = $matcher->matchRequest($request)['controller'];

        $response = new Response($controller->apply(new ActionContext($reqCtx)));

        $response->send();

    }

} 