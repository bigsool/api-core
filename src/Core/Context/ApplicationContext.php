<?php


namespace Core\Context;


use Core\Action\Action;
use Core\ActionQueue;
use Core\Config\ConfigManager;
use Core\Controller;
use Core\Error\ErrorManager;
use Core\Field\Field;
use Core\Filter\Filter;
use Core\Logger\ErrorLogger;
use Core\Logger\Logger;
use Core\Logger\RequestLogger;
use Core\Logger\SQLLogger;
use Core\Logger\TraceLogger;
use Core\Registry;
use Core\Rule\Rule;
use Core\RuleProcessor;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class ApplicationContext {

    /**
     * @var ApplicationContext
     */
    protected static $instance;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var RuleProcessor
     */
    protected $ruleProcessor;

    /**
     * @var Field[]
     */
    protected $fields = [];

    /**
     * @var Filter[]
     */
    protected $filters = [];

    /**
     * @var Rule[]
     */
    protected $rules = [];

    /**
     * @var Action[]
     */
    protected $actions = [];

    /**
     * @var RouteCollection
     */
    protected $routes;

    /**
     * @var ErrorManager
     */
    protected $errorManager;

    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * @var RequestLogger
     */
    protected $queryLogger;

    /**
     * @var ErrorLogger
     */
    protected $errorLogger;

    /**
     * @var SQLLogger
     */
    protected $sqlLogger;

    /**
     * @var TraceLogger
     */
    protected $traceLogger;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var object[]
     */
    protected $helpers = [];

    /**
     * @var string
     */
    protected $sessionId;

    /**
     * @var ActionQueue
     */
    protected $onSuccessActionQueue;

    /**
     * @var ActionQueue
     */
    protected $onErrorActionQueue;

    /**
     * @var string
     */
    protected $product;

    /**
     *
     */
    protected function __construct () {

        $this->loadConstants();

        $this->routes = new RouteCollection();

    }

    protected function loadConstants () {

        require __DIR__ . '/../../../config/constants.php';

    }

    /**
     * @return Logger
     */
    public function getLogger () {

        if (!isset($this->logger)) {
            $this->logger = new Logger();
            $this->logger->setSessionId($this->getSessionId());
        }

        return $this->logger;

    }

    public function getSessionId () {

        if (!isset($this->sessionId)) {
            $this->sessionId = uniqid();
        }

        return $this->sessionId;

    }

    /**
     * @return ErrorLogger
     */
    public function getErrorLogger () {

        if (!isset($this->errorLogger)) {
            $this->errorLogger = new ErrorLogger();
            $this->errorLogger->setSessionId($this->getSessionId());
        }

        return $this->errorLogger;

    }

    /**
     * @return TraceLogger
     */
    public function getTraceLogger () {

        if (!isset($this->traceLogger)) {
            $this->traceLogger = new TraceLogger();
            $this->traceLogger->setSessionId($this->getSessionId());
        }

        return $this->traceLogger;

    }

    /**
     * @return RuleProcessor
     */
    public function getRuleProcessor () {

        return $this->ruleProcessor;

    }

    /**
     * @param RuleProcessor $ruleProcessor
     */
    public function setRuleProcessor (RuleProcessor $ruleProcessor) {

        $this->ruleProcessor = $ruleProcessor;

    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager (EntityManager $entityManager) {

        $this->entityManager = $entityManager;
        $entityManager->getConfiguration()->setSQLLogger($this->getSQLLogger());

    }

    /**
     * @return SQLLogger
     */
    public function getSQLLogger () {

        if (!isset($this->sqlLogger)) {
            $this->sqlLogger = new SQLLogger();
            $this->sqlLogger->setSessionId($this->getSessionId());
        }

        return $this->sqlLogger;

    }

    /**
     * @param string $class
     *
     * @return \Doctrine\ORM\Mapping\ClassMetadata
     */
    public function getClassMetadata ($class) {

        return $this->entityManager->getClassMetadata($class);

    }

    /**
     * @return Registry
     */
    public function getNewRegistry () {

        $registry = new Registry($this->entityManager, $this);

        $this->entityManager->getEventManager()->addEventSubscriber($registry);

        return $registry;

    }

    /**
     * @param Filter $filter
     */
    public function addFilter (Filter $filter) {

        $this->filters[] = $filter;

    }

    /**
     * @param string $entity
     * @param string $name
     *
     * @return Filter
     */
    public function getFilterByEntityAndName ($entity, $name) {

        foreach ($this->getFilters() as $filter) {
            if ($filter->getEntity() == $entity && $filter->getName() == $name) {
                return $filter;
            }
        }

        throw new \RuntimeException('Filter not found');

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return $this->filters;

    }

    /**
     * @param Rule $rule
     */
    public function addRule (Rule $rule) {

        $this->rules[] = $rule;

    }

    /**
     * @return Rule[]
     */
    public function getRules () {

        return $this->rules;

    }

    /**
     * @param Action $theAction
     */
    public function addAction (Action $theAction) {

        $i = 0;
        foreach ($this->actions as $action) {
            if ($action->getModule() == $theAction->getModule() && $action->getName() == $theAction->getName()) {
                //$this->actions[$i] = $theAction;
                //return;
                throw new \RuntimeException('action already defined for this module and name (' . $action->getModule()
                                            . ',' . $action->getName() . ')');
            }
            ++$i;
        }
        if (!in_array($theAction, $this->getActions(), true)) {
            $this->actions[] = $theAction;
        }

    }

    /**
     * @return Action[]
     */
    public function getActions () {

        return $this->actions;

    }

    /**
     * @param $module
     * @param $name
     *
     * @return Action
     */
    public function getAction ($module, $name) {

        foreach ($this->getActions() as $action) {
            if ($action->getModule() == $module && $action->getName() == $name) {
                return $action;
            }
        }

        throw new \RuntimeException('Action not found');

    }

    /**
     * @param string $name
     * @param object $helper
     */
    public function addHelper ($module, $name, $helper) {

        if (!isset($this->helpers[$name])) {
            $this->helpers[$module][$name] = $helper;
        }

    }

    /**
     * @return object[]
     */
    public function getHelpers () {

        return $this->helpers;

    }

    /**
     * @param Action $action
     * @param string $name
     *
     * @return object
     */
    public function getHelper (Action $action,$name) {

        if (isset($this->helpers[$action->getModule()][$name])) {
            return $this->helpers[$action->getModule()][$name];
        }

        throw new \RuntimeException('Helper not found');

    }

    /**
     * @param $path
     * @param $controller
     * @param $action
     */
    public function addRoute ($path, $controller, $action) {

        $camelizedPath = str_replace(' ', '', ucwords(str_replace('/', ' ', $path)));
        $product = ApplicationContext::getInstance()->getProduct();
        $this->routes->add($camelizedPath, new Route($path, [
            'controller' => new Controller($action, $product . '\\' . $controller)
        ]));

    }

    /**
     * @return string
     */
    public function getProduct () {

        return $this->product;
    }

    /**
     * @param string $product
     */
    public function setProduct ($product) {

        $this->product = $product;
    }

    /**
     * @return ApplicationContext
     */
    public static function getInstance () {

        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;

    }

    /**
     * @return RouteCollection
     */
    public function getRoutes () {

        return $this->routes;

    }

    /**
     * @return ErrorManager
     */
    public function getErrorManager () {

        if (!isset($this->errorManager)) {
            $errorManager = new \ReflectionClass('\Core\Error\ErrorManager');
            $this->errorManager = $errorManager->newInstanceWithoutConstructor();
            $constructor = $errorManager->getConstructor();
            $constructor->setAccessible(true);
            $constructor->invoke($this->errorManager);
            $constructor->setAccessible(false);
        }

        return $this->errorManager;

    }

    public function getConfigManager () {

        if (!isset($this->configManager)) {
            $configFiles = [];
            foreach ([ROOT_DIR . '/vendor/api/core/config/', ROOT_DIR . '/config/'] as $coreConfDir) {
                if (file_exists($configFile = $coreConfDir . 'default.yml')) {
                    $configFiles[] = $configFile;
                }
                $configPath = NULL;
                $dbPath = NULL;
                switch ($this->getEnv()) {
                    case LOCAL_ENV:
                        $configPath = $coreConfDir . 'local/config.yml';
                        $dbPath = $coreConfDir . 'local/extra.yml';
                        break;
                    case DEV_ENV:
                        $configPath = $coreConfDir . 'dev/config.yml';
                        $dbPath = $coreConfDir . 'dev/extra.yml';
                        break;
                    case STAGE_ENV:
                        $configPath = $coreConfDir . 'stage/config.yml';
                        $dbPath = $coreConfDir . 'stage/extra.yml';
                        break;
                    case PROD_ENV:
                        $configPath = $coreConfDir . 'prod/config.yml';
                        $dbPath = $coreConfDir . 'prod/extra.yml';
                        break;
                }
                if (!is_null($configPath) && file_exists($configPath)) {
                    $configFiles[] = $configPath;
                }
                if (!is_null($dbPath) && file_exists($dbPath)) {
                    $configFiles[] = $dbPath;
                }

            }

            $this->configManager = new ConfigManager($configFiles);
        }

        return $this->configManager;

    }

    public function getEnv () {

        if (isset($_SERVER['SERVER_NAME'])) {
            // php called by apache
            if ($_SERVER['SERVER_NAME'] == 'localhost'
                || $_SERVER['SERVER_NAME'] == '127.0.0.1'
                || substr($_SERVER['SERVER_NAME'], 0, 3) == '10.'
                || substr($_SERVER['SERVER_NAME'], 0, 8) == '192.168.'
            ) {
                return LOCAL_ENV;
            }
            elseif ($_SERVER['SERVER_NAME'] == 'dev.api.archipad-services.com') {
                return DEV_ENV;
            }
            elseif ($_SERVER['SERVER_NAME'] == 'stage.api.archipad-services.com') {
                return STAGE_ENV;
            }
            else {
                return PROD_ENV;
            }
        }
        else {
            $product = $this->getProduct();
            $sep = DIRECTORY_SEPARATOR;
            // php called from the command line
            if (strpos(__DIR__, $sep . 'api' . $sep . strtolower($product) . $sep) !== false) {
                return LOCAL_ENV;
            }
            elseif (strpos(__DIR__, 'dev-api') !== false) {
                return DEV_ENV;
            }
            elseif (strpos(__DIR__, 'stage-api') !== false) {
                return STAGE_ENV;
            }
            else {
                return PROD_ENV;
            }
        }

    }

    /**
     * @return RequestLogger
     */
    public function getQueryLogger () {

        if (!isset($this->queryLogger)) {
            $this->queryLogger = new RequestLogger();
            $this->queryLogger->setSessionId($this->getSessionId());
        }

        return $this->queryLogger;

    }

    /**
     * @return ActionQueue
     */
    public function getOnSuccessActionQueue () {

        if (!isset($this->onSuccessActionQueue)) {
            $this->onSuccessActionQueue = new ActionQueue();
        }

        return $this->onSuccessActionQueue;

    }

    /**
     * @return ActionQueue
     */
    public function getOnErrorActionQueue () {

        if (!isset($this->onErrorActionQueue)) {
            $this->onErrorActionQueue = new ActionQueue();
        }

        return $this->onErrorActionQueue;

    }

} 