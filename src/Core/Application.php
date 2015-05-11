<?php


namespace Core;


use Core\Action\Action;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\RequestContext;
use Core\Error\FormattedError;
use Core\Error\ToResolveException;
use Core\Field\RelativeField;
use Core\Module\ModuleManager;
use Core\RPC\Handler;
use Core\RPC\JSON;
use Core\Rule\Processor;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext as SfRequestContext;

class Application {

    /**
     * @var Application
     */
    protected static $instance;

    /**
     * @var ModuleManager[]
     */
    protected $moduleManagers = [];

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var ApplicationContext
     */
    protected $appCtx;

    /**
     * @return Application
     */
    public static function getInstance () {

        if (!isset(static::$instance)) {
            static::$instance = new static();
            static::$instance->appCtx = static::$instance->createApplicationContext();
        }

        return static::$instance;

    }

    protected function __construct () {

        static::defineRootDir();

        if (function_exists('opcache_get_status')) {
            $opcacheStatus = opcache_get_status(false);
            $opcacheStatistics = $opcacheStatus['opcache_statistics'];
            $realpath = realpath(ROOT_DIR);
            $dirname = dirname($realpath);
            $exploded = explode('-', basename($realpath), 3);
            if (isset($exploded[2])) {
                unset($exploded[2]);
            }
            $basename = implode('-', $exploded);
            $path = $dirname . '/' . $basename;
            $fileTimestamp = filemtime($path);
            $opcacheStartTime = $opcacheStatistics['last_restart_time'] ?: $opcacheStatistics['start_time'];
            if ($opcacheStartTime < $fileTimestamp) {
                opcache_reset();
            }
        }

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
        $this->appCtx->setApplication($this);

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
        $this->appCtx->setRuleProcessor(new Processor());

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

            $this->loadModules();

            // default RPCHandler
            $rpcHandler = new JSON();

            $reqCtx = new RequestContext();

            try {

                $request = Request::createFromGlobals();
                $this->appCtx->getQueryLogger()->logRequest($request);

                $rpcHandler = $this->getRPCHandlerFromHTTPRequest($request);

                $this->populateRequestContext($rpcHandler, $reqCtx);

                $sfReqCtx = new SfRequestContext();
                $sfReqCtx->fromRequest($request);

                $controller = $this->getController($sfReqCtx, $rpcHandler, $reqCtx);

                $response = $this->executeController($controller, $reqCtx, $rpcHandler);

            }
            catch (\Exception $e) {

                $response = $this->handleException($reqCtx, $e, $rpcHandler);

            }

            $this->appCtx->getQueryLogger()->logResponse($response);

            if (ob_get_length()) {
                $logger->addWarning("buffer isn't empty:" . ob_get_contents());
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
     *
     */
    protected function loadModules () {

        foreach ($this->getModuleManagers() as $moduleManager) {
            $moduleManager->load($this->appCtx);
        }

        $this->appCtx->getTraceLogger()->trace('modules loaded');

    }

    /**
     * @return ModuleManager[]
     */
    public function getModuleManagers () {

        if (!$this->moduleManagers) {
            $product = $this->appCtx->getProduct();
            $modules = array_map('basename', glob(ROOT_DIR . '/src/' . $product . '/Module/*', GLOB_ONLYDIR));
            $this->moduleManagers = [];
            foreach ($modules as $moduleName) {
                if ($product != 'Core') {
                    $className = "\\Core\\Module\\$moduleName\\ModuleManager";
                    if (class_exists($className)) {
                        $this->moduleManagers[] = new $className;
                    }
                }
                $className = "\\$product\\Module\\$moduleName\\ModuleManager";
                $this->moduleManagers[] = new $className;
            }
        }

        return $this->moduleManagers;

    }

    /**
     * @param Request $request
     *
     * @return Handler
     * @throws FormattedError
     *
     */
    protected function getRPCHandlerFromHTTPRequest (Request $request) {

        $rpcHandler = $this->getRPCHandlerForProtocol($this->findProtocolInRequest($request));

        $rpcHandler->parse($request);
        $this->appCtx->getTraceLogger()->trace('request parsed');

        return $rpcHandler;

    }

    /**
     * @param string $protocol
     *
     * @return Handler
     * @throws FormattedError
     */
    protected function getRPCHandlerForProtocol ($protocol) {

        $rpcClassName = '\Core\RPC\\' . $protocol;
        if (!$protocol || !class_exists($rpcClassName)) {
            throw $this->appCtx->getErrorManager()->getFormattedError(ERROR_PROTOCOL_IS_INVALID);
        }

        $rpcHandler = new $rpcClassName();

        if (!($rpcHandler instanceof Handler)) {
            throw $this->appCtx->getErrorManager()->getFormattedError(ERROR_PROTOCOL_IS_INVALID);
        }

        return $rpcHandler;

    }

    /**
     * @param Request $request
     *
     * @return string
     */
    protected function findProtocolInRequest (Request $request) {

        $protocol = strstr(trim($request->getPathInfo(), '/'), '/', true);

        return $protocol;

    }

    /**
     * @param Handler        $rpcHandler
     * @param RequestContext $reqCtx
     *
     * @throws FormattedError
     */
    protected function populateRequestContext (Handler $rpcHandler, RequestContext &$reqCtx) {

        $reqCtx->setParams($rpcHandler->getParams());
        $reqCtx->setClientName($rpcHandler->getClientName());
        $reqCtx->setClientVersion($rpcHandler->getClientVersion());
        $reqCtx->setLocale($rpcHandler->getLocale());
        $reqCtx->setIpAddress($rpcHandler->getIpAddress());
        $reqCtx->setReturnedFields(array_map(function ($field) {

            return new RelativeField($field);

        }, $rpcHandler->getReturnedFields()));

    }

    /**
     * @param SfRequestContext $sfReqCtx
     * @param Handler          $rpcHandler
     * @param RequestContext   $reqCtx
     *
     * @return Controller
     * @throws FormattedError
     */
    protected function getController (SfRequestContext $sfReqCtx, Handler $rpcHandler, RequestContext &$reqCtx) {

        $matcher = new UrlMatcher($this->appCtx->getRoutes(), $sfReqCtx);

        /**
         * @var Controller $controller
         */
        try {
            $match = $matcher->match($rpcHandler->getPath());
            if (isset($match['fields']) && !$reqCtx->getReturnedFields()) {
                $fields = [];
                foreach ($match['fields'] as $field) {
                    $fields[] = new RelativeField($field);
                }
                $reqCtx->setReturnedFields($fields);
            }
            $controller = $match['controller'];
            $this->appCtx->getTraceLogger()->trace('controller found');
        }
        catch (\Exception $e) {
            throw $this->appCtx->getErrorManager()->getFormattedError(ERROR_METHOD_NOT_FOUND);
        }

        return $controller;

    }

    /**
     * @param Controller     $controller
     * @param RequestContext $reqCtx
     * @param Handler        $rpcHandler
     *
     * @return Response
     */
    protected function executeController (Controller $controller, RequestContext $reqCtx, Handler $rpcHandler) {

        $traceLogger = $this->appCtx->getTraceLogger();

        $action = $controller->getAction();
        $actCtx = $reqCtx->getApplicationContext()->getActionContext($reqCtx, $action->getModule(), $action->getName());
        $result = $controller->apply($actCtx);
        $traceLogger->trace('controller called');

        $response = $rpcHandler->getSuccessResponse($this->getSerializer($actCtx), $result);
        $reqCtx->setResponse($response);
        $traceLogger->trace('response created');

        // handle queued actions before commit
        $this->executeSuccessQueuedActions($reqCtx);

        $this->entityManager->commit();
        $traceLogger->trace('database committed');

        return $response;

    }

    /**
     * @param ActionContext $actCtx
     *
     * @return Serializer
     */
    protected function getSerializer (ActionContext $actCtx) {

        return new Serializer($actCtx);

    }

    /**
     * @param RequestContext $reqCtx
     */
    protected function executeSuccessQueuedActions (RequestContext $reqCtx) {

        $queue = $this->appCtx->getOnSuccessActionQueue();
        while (!$queue->isEmpty()) {
            /**
             * @var Action $action
             */
            list($action, $params) = $queue->dequeue();
            $appCtx = $reqCtx->getApplicationContext();
            $ctx = $appCtx->getActionContext($reqCtx, $action->getModule(), $action->getName());
            $ctx->setParams($params);
            $action->process($ctx);
        }

        $this->appCtx->getTraceLogger()->trace('success queue processed');

    }

    /**
     * @param RequestContext $reqCtx
     * @param \Exception     $e
     * @param Handler        $rpcHandler
     *
     * @return Response
     * @internal param $traceLogger
     */
    protected function handleException (RequestContext $reqCtx, \Exception $e, Handler $rpcHandler) {

        $traceLogger = $this->appCtx->getTraceLogger();


        // handle queued actions before commit
        $this->executeErrorQueuedActions($reqCtx);

        if ($e instanceof ToResolveException) {

            $traceLogger->trace('ToResolveException thrown');

            $errMgr = ApplicationContext::getInstance()->getErrorManager();
            $e = $errMgr->getFormattedError($e->getErrorCode(), $e->getField());

        }

        if ($e instanceof FormattedError) {

            $traceLogger->trace('FormattedError thrown');
            $response = $rpcHandler->getErrorResponse($e);

        }
        else {

            $traceLogger->trace('Unexpected Exception thrown');
            $this->appCtx->getErrorLogger()->getMLogger()->addError('Unexpected Exception thrown', [get_class($e),
                                                                                                    $e->getCode(),
                                                                                                    $e->getMessage(),
                                                                                                    $e->getFile(),
                                                                                                    $e->getLine(),
                                                                                                    $e->getTraceAsString()
            ]);
            $response = $rpcHandler->getErrorResponse(new FormattedError(['code'    => ERROR_INTERNAL_ERROR,
                                                                          'message' => $e->getMessage()
                                                                         ]));

        }

        $traceLogger->trace('response created');

        return $response;

    }

    /**
     * @param RequestContext $reqCtx
     */
    protected function executeErrorQueuedActions (RequestContext $reqCtx = NULL) {

        if (!isset($reqCtx)) {
            $reqCtx = new RequestContext();
        }

        $queue = $this->appCtx->getOnErrorActionQueue();
        while (!$queue->isEmpty()) {
            /**
             * @var Action $action
             */
            list($action, $params) = $queue->dequeue();
            $appCtx = $reqCtx->getApplicationContext();
            $ctx = $appCtx->getActionContext($reqCtx, $action->getModule(), $action->getName());
            $ctx->setParams($params);
            $action->process($ctx);
        }

        $this->appCtx->getTraceLogger()->trace('error queue processed');

    }

}