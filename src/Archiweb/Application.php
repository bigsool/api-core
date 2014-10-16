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

    /**
     * @var ApplicationContext
     */
    protected $appCtx;

    public function __construct () {

        $this->appCtx = $this->createApplicationContext();

    }

    /**
     * @return ApplicationContext
     */
    protected function createApplicationContext () {

        $this->appCtx = ApplicationContext::getInstance();

        require __DIR__ . '/../../doctrine/config.php';
        require_once __DIR__ . '/../../config/errors.php';
        loadErrors($this->appCtx->getErrorManager());


        /**
         * @var EntityManager $entityManager ;
         */
        $this->appCtx->setEntityManager($entityManager);
        $this->appCtx->setRuleProcessor(new RuleProcessor());

        return $this->appCtx;

    }

    /**
     *
     */
    public function run () {

        try {

            $modules = array_map('basename', glob(__DIR__ . '/Module/*', GLOB_ONLYDIR));
            foreach ($modules as $moduleName) {
                $className = "\\Archiweb\\Module\\$moduleName\\ModuleManager";
                /**
                 * @var ModuleManager $moduleManager
                 */
                $moduleManager = new $className;
                $moduleManager->load($this->appCtx);
            }

            try {
                $request = Request::createFromGlobals();
                $sfReqCtx = new SymfonyRequestContext();
                $sfReqCtx->fromRequest($request);

                $rpcHandler = new JSONP($this->appCtx, $request);

                $matcher = new UrlMatcher($this->appCtx->getRoutes(), $sfReqCtx);

                $reqCtx = new RequestContext($this->appCtx);
                $reqCtx->setParams($rpcHandler->getParams());

                /**
                 * @var Controller $controller
                 */
                try {
                    $controller = $matcher->match($rpcHandler->getPath())['controller'];
                }
                catch (\Exception $e) {
                    throw $this->appCtx->getErrorManager()->getFormattedError(ERR_METHOD_NOT_FOUND);
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

} 