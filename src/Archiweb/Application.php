<?php


namespace Archiweb;


use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Context\FindQueryContext;
use Archiweb\Context\RequestContext;
use Archiweb\Error\FormattedError;
use Archiweb\Field\KeyPath as FieldKeyPath;
use Archiweb\Field\KeyPath;
use Archiweb\Filter\StringFilter;
use Archiweb\Module\ModuleManager;
use Archiweb\RPC\Handler;
use Archiweb\RPC\JSON;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext as SymfonyRequestContext;

define('ROOT_DIR', __DIR__ . '/../..');

class Application {

    /**
     * @var EntityManager
     */
    protected $entityManager;

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

        // This will log routes
        $this->appCtx->getConfigManager();

        require ROOT_DIR . '/doctrine/config.php';
        if (require_once ROOT_DIR . '/config/errors.php') {
            loadErrors($this->appCtx->getErrorManager());
        }


        /**
         * @var EntityManager $entityManager ;
         */
        $this->appCtx->setEntityManager($this->entityManager = $entityManager);
        $this->appCtx->setRuleProcessor(new RuleProcessor());

        $entityManager->beginTransaction();

        return $this->appCtx;

    }

    /**
     *
     */
    public function run () {

        $traceLogger = $this->appCtx->getTraceLogger();

        $traceLogger->trace('start run');

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

            $traceLogger->trace('modules loaded');

            // default RPCHandler
            $rpcHandler = new JSON();

            try {
                $request = Request::createFromGlobals();
                $sfReqCtx = new SymfonyRequestContext();
                $sfReqCtx->fromRequest($request);

                $this->appCtx->getQueryLogger()->logRequest($request);

                $protocol = strstr(trim($request->getPathInfo(), '/'), '/', true);
                $rpcClassName = '\Archiweb\RPC\\' . $protocol;
                if (!$protocol || !class_exists($rpcClassName)) {
                    throw $this->appCtx->getErrorManager()->getFormattedError(ERR_PROTOCOL_IS_INVALID);
                }

                $rpcHandler = new $rpcClassName();
                /**
                 * @var Handler $rpcHandler
                 */

                $rpcHandler->parse($request);

                $matcher = new UrlMatcher($this->appCtx->getRoutes(), $sfReqCtx);

                $reqCtx = new RequestContext();
                $reqCtx->setParams($rpcHandler->getParams());
                $reqCtx->setReturnedRootEntity($rpcHandler->getReturnedRootEntity());
                $reqCtx->setReturnedKeyPaths(array_map(function ($field) {

                    return new KeyPath($field);

                }, $rpcHandler->getReturnedFields()));

                $traceLogger->trace('request parsed');

                /**
                 * @var Controller $controller
                 */
                try {
                    $controller = $matcher->match($rpcHandler->getPath())['controller'];
                    $traceLogger->trace('controller found');
                }
                catch (\Exception $e) {
                    throw $this->appCtx->getErrorManager()->getFormattedError(ERR_METHOD_NOT_FOUND);
                }
                $actCtx = new ActionContext($reqCtx);

                $result = $controller->apply($actCtx);

                $traceLogger->trace('controller called');

                $serializer = new Serializer($reqCtx);

                $response = $rpcHandler->getSuccessResponse($serializer, $result);

                $traceLogger->trace('response created');

                $this->entityManager->commit();

                $traceLogger->trace('database committed');

            }
            catch (FormattedError $e) {

                $traceLogger->trace('FormattedError thrown');

                $response = $rpcHandler->getErrorResponse($e);

                $traceLogger->trace('response created');

            }
            catch (\Exception $e) {

                $traceLogger->trace('Exception thrown');

                $response = $rpcHandler->getErrorResponse(new FormattedError(['code'    => $e->getCode(),
                                                                              'message' => $e->getMessage()
                                                                             ]));

                $traceLogger->trace('response created');
            }

            $this->appCtx->getQueryLogger()->logResponse($response);

            $response->send();

            $traceLogger->trace('response sent');

        }
        catch (\Exception $e) {

            $this->appCtx->getLogger()->getMLogger()->addEmergency(json_encode(['code'       => $e->getCode(),
                                                                            'message'    => $e->getMessage(),
                                                                            'stackTrace' => $e->getTraceAsString()
                                                                           ]));

            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            exit('Internal Server Error');

        }

    }

    protected function getAuth ($name) {

        $findCtx = new FindQueryContext('User');
        $findCtx->addFilter(new StringFilter('User', '', 'name = "' . $name . '"'));
        $findCtx->addKeyPath(new FieldKeyPath('*'));
        $user = $this->appCtx->getNewRegistry()->find($findCtx, false);

        return $user;

    }

} 