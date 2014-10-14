<?php


namespace Archiweb;


use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Context\RequestContext;
use Archiweb\Error\FormattedError;
use Archiweb\Module\ModuleManager;
use Archiweb\RPC\JSONP;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext as SymfonyRequestContext;

class Application {

    public function run () {

        try {

            $appCtx = $this->createApplicationContext();

            $modules = array_map('basename', glob(__DIR__ . '/Module/*', GLOB_ONLYDIR));
            foreach ($modules as $moduleName) {
                $className = "\\Archiweb\\Module\\$moduleName\\ModuleManager";
                /**
                 * @var ModuleManager $moduleManager
                 */
                $moduleManager = new $className;
                $moduleManager->load($appCtx);
            }

            try {
                $request = Request::createFromGlobals();
                $sfReqCtx = new SymfonyRequestContext();
                $sfReqCtx->fromRequest($request);

                $rpcHandler = new JSONP($appCtx, $request);

                $matcher = new UrlMatcher($appCtx->getRoutes(), $sfReqCtx);

                $reqCtx = new RequestContext($appCtx);
                $reqCtx->setParams($rpcHandler->getParams());

                /**
                 * @var Controller $controller
                 */
                try {
                    $controller = $matcher->match($rpcHandler->getPath())['controller'];
                }
                catch (\Exception $e) {
                    throw $appCtx->getErrorManager($reqCtx)->getFormattedError(ERR_METHOD_NOT_FOUND);
                }


                $result = $controller->apply(new ActionContext($reqCtx));
                $response = new Response(json_encode($result));

                $response->send();
            }
            catch (FormattedError $e) {
                (new Response((string)$e))->send();
            }
            catch (\Exception $e) {
                (new Response(json_encode(['code' => $e->getCode(), 'message' => $e->getMessage()])))->send();
            }

        }
        catch (\Exception $e) {

            exit('fatal error');

        }

    }

    /**
     * @return ApplicationContext
     */
    protected function createApplicationContext () {

        $appCtx = new ApplicationContext();

        require __DIR__ . '/../../doctrine/config.php';

        /**
         * @var EntityManager $entityManager ;
         */
        $appCtx->setEntityManager($entityManager);
        $appCtx->setRuleProcessor(new RuleProcessor());

        return $appCtx;

    }

} 