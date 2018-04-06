<?php


namespace Core;


use Core\Action\Action;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\RequestContext;
use Core\Error\FormattedError;
use Core\Error\ToResolveException;
use Core\Error\ValidationException;
use Core\Field\RelativeField;
use Core\Module\MagicalModuleManager;
use Core\Module\ModuleManager;
use Core\RPC\CLI;
use Core\RPC\Handler;
use Core\RPC\JSON;
use Core\RPC\Local;
use Core\Rule\Processor;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\ConfigCacheFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext as SfRequestContext;
use Symfony\Component\Translation\Loader\PoFileLoader;
use Symfony\Component\Translation\Translator;

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

    /**
     *
     */
    protected function __construct () {

        static::defineRootDir();

        if (function_exists('opcache_get_status')) {
            $opcacheStatus = opcache_get_status(false);
            $opcacheStatistics = $opcacheStatus['opcache_statistics'];
            $path = realpath(ROOT_DIR);
            clearstatcache(true, $path);
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
    protected function getFirstApplicationContextInstance () {

        return ApplicationContext::getInstance();

    }

    /**
     * @return ApplicationContext
     */
    protected function createApplicationContext () {

        $this->appCtx = $this->getFirstApplicationContextInstance();
        $this->appCtx->setApplication($this);

        set_error_handler($this->appCtx->getErrorLogger()->getErrorHandler());
        set_exception_handler($this->appCtx->getErrorLogger()->getExceptionHandler());
        register_shutdown_function($this->appCtx->getErrorLogger()->getShutdownFunction());

        // This will log routes
        $this->appCtx->getConfigManager();

        $this->appCtx->getTraceLogger()->trace('config loaded');

        $this->initTranslation();

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
    public function initTranslation () {

        $key = 'TRANSLATION';

        $config = $this->appCtx->getConfigManager()->getConfig();
        // Guarantee having a trailing slash
        $translationsPath = realpath(ROOT_DIR . '/' . $config['resourcePaths']['translations']).'/';
        $cacheProvider = $this->appCtx->getCacheProvider();
        $translations  = $cacheProvider->fetch($key);
        $languageCodes = [];

        if ($translations === false) {
            foreach (new \DirectoryIterator($translationsPath) as $file) {
                if ($file->getExtension() === 'po') {
                    $languageCodes[] = rtrim($file->getFilename(), '.po');
                }
            }
            // Scanning, loading
            // trade-off, if we change the config without clearing the cache, it will take up to 10m to be updated
            $cacheProvider->save($key, $languageCodes, 600);
        } else {
            $languageCodes = $translations;
        }

        $defaultLocale = 'en';
        $translator = new Translator($defaultLocale);
        $translator->setFallbackLocales([$defaultLocale]);
        $translator->setConfigCacheFactory(new ConfigCacheFactory(false));
        $translator->addLoader('po', new PoFileLoader());

        foreach ($languageCodes as $code) {
            $path = $translationsPath.$code.'.po';

            if (!file_exists($path)) {
                throw new \RuntimeException(
                    sprintf('Entry %s is in cache but cannot be found in filesystem. Did you remove it ?', $code)
                );
            }

            $translator->addResource('po', $path, $code);
        }

        $this->appCtx->setTranslator($translator);

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

            $reqCtx = $this->getNewRequestContext();

            try {

                $request = Request::createFromGlobals();
                $this->appCtx->getQueryLogger()->logRequest($request);

                if (strtoupper($_SERVER['REQUEST_METHOD']) == 'OPTIONS') {

                    $response = new Response('', Response::HTTP_OK);
                }
                else {

                    $rpcHandler = $this->getRPCHandlerFromHTTPRequest($request);

                    $this->appCtx->getTranslator()->setLocale($rpcHandler->getLocale());

                    // handle API is temporary down during deploy
                    $config = $this->appCtx->getConfigManager()->getConfig();
                    if (isset($config['down']) && $config['down']) {
                        throw new ToResolveException(ERROR_API_UNAVAILABLE);
                    }

                    $reqCtx->getApplicationContext()->setInitialRequestContext($reqCtx);

                    $this->populateRequestContext($rpcHandler, $reqCtx);

                    $sfReqCtx = new SfRequestContext();
                    $sfReqCtx->fromRequest($request);

                    $controller = $this->getController($sfReqCtx, $rpcHandler, $reqCtx);

                    $response = $this->executeController($controller, $reqCtx, $rpcHandler);

                }

            }
            catch (\Exception $e) {

                $response = $this->handleException($reqCtx, $e, $rpcHandler);

                // handle function which must be executed after the commit/rollback
                $this->executeFunctionsAfterCommitOrRollback(false);

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

            // handle function which must be executed after the commit/rollback
            $this->executeFunctionsAfterCommitOrRollback(false);

            header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
            exit('Internal Server Error');

        }

    }

    /**
     * @param Local $rpcHandler
     *
     * @return Response
     * @throws \Exception
     */
    public function runWithCustomRPCHandler (Local $rpcHandler) {

        // if the transaction was previously commit/rollback then begin a new one
        if ($this->entityManager->getConnection()->getTransactionNestingLevel() == 0) {
            $this->entityManager->beginTransaction();
        }

        $traceLogger = $this->appCtx->getTraceLogger();

        $logger = $this->appCtx->getLogger()->getMLogger();

        $traceLogger->trace('start run');

        try {

            $this->loadModules();

            $reqCtx = $this->getNewRequestContext();
            $reqCtx->setAuth(Auth::createInternalAuth(TRUE));

            $reqCtx->getApplicationContext()->setInitialRequestContext($reqCtx);

            $rpcHandler->parse(new Request());
            $this->populateRequestContext($rpcHandler, $reqCtx);

            $this->appCtx->getTranslator()->setLocale($reqCtx->getLocale());

            $controller = new Controller($rpcHandler->getAction(), $rpcHandler->getModule());

            $this->executeController($controller, $reqCtx, $rpcHandler);

            return $rpcHandler->getResult();

        }
        catch (\Exception $e) {

            $logger->addEmergency(json_encode(['code'       => $e->getCode(),
                                               'message'    => $e->getMessage(),
                                               'stackTrace' => $e->getTraceAsString()
                                              ]));

            throw $e;

        }

    }

    /**
     * @return Response
     */
    public function runCli () {

        try {
            return $this->runWithCustomRPCHandler(new Cli);
        }
        catch (\Exception $e) {
            exit('Internal Server Error');
        }

    }

    /**
     *
     */
    protected function loadModules () {

        // Module already added
        if ($this->moduleManagers) {
            return;
        }

        foreach ($this->getModuleManagers() as $moduleManager) {
            $moduleManager->load($this->appCtx);
        }

        // loading of model aspect must be done after the definition of all Module Entities
        // so loadModuleEntities is called later by Application
        foreach ($this->moduleManagers as $moduleManager) {
            if ($moduleManager instanceof MagicalModuleManager) {
                $moduleManager->loadModelAspects($this->appCtx);
            }
        }

        // loading of calculated fields must be done after the load of model aspects
        foreach ($this->moduleManagers as $moduleManager) {
            foreach ($moduleManager->getModuleEntities() as $moduleEntity) {
                $dbEntityName = $moduleEntity->getDefinition()->getDBEntityName();
                foreach ($moduleEntity->getDefinition()->getFields() as $fieldName => $calculatedField) {
                    $calculatedField->setFieldName($fieldName);
                    $this->appCtx->addCalculatedField($dbEntityName, $fieldName, $calculatedField);
                }
            }
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

        $reqCtx->setClientName($rpcHandler->getClientName());
        $reqCtx->setClientVersion($rpcHandler->getClientVersion());
        $reqCtx->setLocale($rpcHandler->getLocale());
        $reqCtx->setIpAddress($rpcHandler->getIpAddress());
        $reqCtx->setReturnedFields(array_map(function ($field) {

            return new RelativeField($field);

        }, $rpcHandler->getReturnedFields()));
        $reqCtx->setAuthToken($rpcHandler->getAuthToken());
        $reqCtx->setParams($rpcHandler->getParams());

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

        $rpcHandler->setResult($result);
        $response = $rpcHandler->getSuccessResponse($this->getSerializer($actCtx));
        $reqCtx->setResponse($response);
        $traceLogger->trace('response created');

        // handle queued actions before commit
        $this->executeSuccessQueuedActions($reqCtx);

        $this->entityManager->commit();
        $traceLogger->trace('database committed');

        // handle function which must be
        $this->executeFunctionsAfterCommitOrRollback(true);

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
     * Functions added in this queue must be executed after the commit
     */
    protected function executeFunctionsAfterCommitOrRollback ($success) {

        $queue = $this->appCtx->getFunctionsQueueAfterCommitOrRollback();
        while (!$queue->isEmpty()) {
            $callable = $queue->dequeue();
            call_user_func($callable, $success);
        }

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
        $errorLogger = $this->appCtx->getErrorLogger();


        // handle queued actions before commit
        $this->executeErrorQueuedActions($reqCtx);

        if ($e instanceof ValidationException) {
            $errMgr = $this->appCtx->getErrorManager();
            $errMgr->addErrors($e->getErrors());

            $e = $errMgr->getFormattedError();
        }

        if ($e instanceof ToResolveException) {

            $traceLogger->trace('ToResolveException thrown');

            $errMgr = ApplicationContext::getInstance()->getErrorManager();
            $e = $errMgr->getFormattedError($e->getErrorCode(), $e->getField());

        }

        if ($e instanceof FormattedError) {

            $traceLogger->trace('FormattedError thrown');
            $rpcHandler->setError($e);
            $response = $rpcHandler->getErrorResponse();

        }
        else {

            $traceLogger->trace('Unexpected Exception thrown');
            $errorLogger->getMLogger()->addError('Unexpected Exception thrown', [get_class($e),
                                                                                                    $e->getCode(),
                                                                                                    $e->getMessage(),
                                                                                                    $e->getFile(),
                                                                                                    $e->getLine(),
                                                                                                    $e->getTraceAsString()
            ]);

            $rpcHandler->setError(new FormattedError(['code'    => ERROR_INTERNAL_ERROR,
                                                      'message' => sprintf('Internal Error, please contact us at support@archipad.com with the error number %s.',
                                                                           $errorLogger->getSessionId())
                                                     ]));
            $response = $rpcHandler->getErrorResponse();

        }

        $traceLogger->trace('response created');

        return $response;

    }

    /**
     * @param RequestContext $reqCtx
     */
    protected function executeErrorQueuedActions (RequestContext $reqCtx = NULL) {

        if (!isset($reqCtx)) {
            $reqCtx = $this->getNewRequestContext();
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

    /**
     * @return ApplicationContext
     */
    public function getAppCtx () {

        return $this->appCtx;

    }

    /**
     * @return RequestContext
     */
    protected function getNewRequestContext () {

        return $this->appCtx->getRequestContextFactory()->getNewRequestContext();

    }

}