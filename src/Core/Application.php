<?php


namespace Core;


use Core\Action\Action;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\RequestContext;
use Core\Error\FormattedError;
use Core\Field\KeyPath;
use Core\Logger\TraceLogger;
use Core\Module\ModuleManager;
use Core\RPC\Handler;
use Core\RPC\JSON;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\Matcher\UrlMatcherInterface;
use Symfony\Component\Routing\RequestContext as SymfonyRequestContext;

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

        self::defineRootDir();
        $this->appCtx = $this->createApplicationContext();

    }

    /**
     * Define ROOT_DIR constant which is used by other files
     * This method is static for testing purpose
     */
    public static function defineRootDir () {

        if (!defined('ROOT_DIR')) {
            define('ROOT_DIR', dirname((new \ReflectionClass(get_called_class()))->getFileName()) . '/../..');
        }

    }

    /**
     * @return ApplicationContext
     */
    protected function createApplicationContext () {

        $this->appCtx = ApplicationContext::getInstance();
        $this->appCtx->setProduct(strstr(get_class($this), '\\', true));

        set_error_handler($this->appCtx->getErrorLogger()->getErrorHandler());
        set_exception_handler($this->appCtx->getErrorLogger()->getExceptionHandler());
        register_shutdown_function($this->appCtx->getErrorLogger()->getShutdownFunction());

        // This will log routes
        $this->appCtx->getConfigManager();

        // We should use require_once but some tests will fail if we do that
        require ROOT_DIR . '/doctrine/config.php';
        if (file_exists($errorFile = ROOT_DIR . '/vendor/api/core/config/errors.php')) {
            require $errorFile;
        }
        if (file_exists($errorFile = ROOT_DIR . '/config/errors.php')) {
            require $errorFile;
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

        ob_start();

        $traceLogger = $this->appCtx->getTraceLogger();

        $logger = $this->appCtx->getLogger()->getMLogger();

        $traceLogger->trace('start run');

        try {

            foreach ($this->getModuleManagers() as $moduleManager) {
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

                // we can't override $rpcHandler var until we're sure that the new value is a valid RPCHandler
                // because we require an RPCHandler to send an error
                $tmpRPCHandler = $this->getRPCHandlerForProtocol($protocol);
                if (!($tmpRPCHandler instanceof Handler)) {
                    throw $this->appCtx->getErrorManager()->getFormattedError(ERR_PROTOCOL_IS_INVALID);
                }
                $rpcHandler = $tmpRPCHandler;

                $rpcHandler->parse($request);

                $matcher = new UrlMatcher($this->appCtx->getRoutes(), $sfReqCtx);

                $reqCtx = new RequestContext();
                $reqCtx->setParams($rpcHandler->getParams());
                $reqCtx->setReturnedRootEntity($rpcHandler->getReturnedRootEntity());
                $reqCtx->setReturnedKeyPaths(array_map(function ($field) {

                    return new KeyPath($field);

                }, $rpcHandler->getReturnedFields()));

                $traceLogger->trace('request parsed');
                $controller = $this->getController($matcher, $rpcHandler, $traceLogger);

                $actCtx = new ActionContext($reqCtx);

                $result = $controller->apply($actCtx);

                $traceLogger->trace('controller called');

                $serializer = new Serializer($reqCtx);

                $response = $rpcHandler->getSuccessResponse($serializer, $result);

                $traceLogger->trace('response created');

                // handle queued actions before commit
                $queue = $this->appCtx->getOnSuccessActionQueue();
                while (!$queue->isEmpty()) {
                    /**
                     * @var Action $action
                     */
                    list($action, $params) = $queue->dequeue();
                    $ctx = new ActionContext($reqCtx);
                    $ctx->setParams($params);
                    $action->process($ctx);
                }

                $traceLogger->trace('success queue processed');

                $this->entityManager->commit();

                $traceLogger->trace('database committed');

            }
            catch (\Exception $e) {

                // handle queued actions before commit
                $queue = $this->appCtx->getOnErrorActionQueue();
                while (!$queue->isEmpty()) {
                    /**
                     * @var Action $action
                     */
                    list($action, $params) = $queue->dequeue();
                    if (!isset($reqCtx) || !($reqCtx instanceof RequestContext)) {
                        $reqCtx = new RequestContext();
                    }
                    $ctx = new ActionContext($reqCtx);
                    $ctx->setParams($params);
                    $action->process($ctx);
                }

                $traceLogger->trace('error queue processed');

                if ($e instanceof FormattedError) {

                    $traceLogger->trace('FormattedError thrown');

                    $response = $rpcHandler->getErrorResponse($e);

                }
                else {

                    $traceLogger->trace('Exception thrown');

                    $response = $rpcHandler->getErrorResponse(new FormattedError(['code'    => $e->getCode(),
                                                                                  'message' => $e->getMessage()
                                                                                 ]));

                }

                $traceLogger->trace('response created');
            }

            $this->appCtx->getQueryLogger()->logResponse($response);

            if (ob_get_length()) {
                $logger->addWarning(ob_get_contents());
            }

            ob_end_clean();

            $response->send();

            $traceLogger->trace('response sent');

        }
        catch (\Exception $e) {

            $logger->addEmergency(json_encode(['code'       => $e->getCode(),
                                               'message'    => $e->getMessage(),
                                               'stackTrace' => $e->getTraceAsString()
                                              ]));

            ob_end_clean();

            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            exit('Internal Server Error');

        }

    }

    /**
     * @return ModuleManager[]
     */
    public function getModuleManagers () {

        $product = $this->appCtx->getProduct();
        $modules = array_map('basename', glob(ROOT_DIR . 'src/' . $product . '/Module/*', GLOB_ONLYDIR));
        $moduleManagers = [];
        foreach ($modules as $moduleName) {
            $className = "\\$product\\Module\\$moduleName\\ModuleManager";
            $moduleManagers[] = new $className;
        }

        return $moduleManagers;

    }

    /**
     * @param string $protocol
     *
     * @return null|Handler
     */
    protected function getRPCHandlerForProtocol ($protocol) {

        $rpcClassName = '\Core\RPC\\' . $protocol;
        if (!$protocol || !class_exists($rpcClassName)) {
            return NULL;
        }

        return new $rpcClassName();
    }

    /**
     * @param UrlMatcherInterface $matcher
     * @param Handler             $rpcHandler
     * @param TraceLogger         $traceLogger
     *
     * @return Controller
     * @throws FormattedError
     */
    protected function getController (UrlMatcherInterface $matcher, Handler $rpcHandler, TraceLogger $traceLogger) {

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

        return $controller;
    }

}