<?php


namespace Core\Context;


use Core\Action\Action;
use Core\ActionQueue;
use Core\Application;
use Core\Config\ConfigManager;
use Core\Controller;
use Core\Error\ErrorManager;
use Core\Field\Calculated;
use Core\Filter\Filter;
use Core\FunctionQueue;
use Core\Helper\ClassHelper;
use Core\Helper\ModuleManagerHelperLoader;
use Core\Logger\ErrorLogger;
use Core\Logger\Logger;
use Core\Logger\RequestLogger;
use Core\Logger\SQLLogger;
use Core\Logger\TraceLogger;
use Core\Module\AggregatedModuleEntity;
use Core\Module\AggregatedModuleEntityDefinition;
use Core\Module\DbModuleEntity;
use Core\Module\MagicalEntity;
use Core\Module\MagicalModuleManager;
use Core\Module\ModelAspect;
use Core\Module\ModuleEntity;
use Core\Module\ModuleEntityDefinition;
use Core\Module\ModuleManager;
use Core\Registry;
use Core\Rule\Processor;
use Core\Rule\Rule;
use Core\Serializer;
use Core\Validation\ParameterFactory;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\MemcacheCache;
use Doctrine\Common\Cache\MemcachedCache;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Translation\Translator;

class ApplicationContext {

    const UNIT_TESTS_USER_ARGENT = 'UnitTest/1.0';

    /**
     * @var ApplicationContext
     */
    protected static $instance;

    /**
     * @var Application
     */
    protected $application;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var \Core\Rule\Processor
     */
    protected $ruleProcessor;

    /**
     * @var Calculated[][]
     */
    protected $calculatedFields;

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
     * @var Action[][]
     */
    protected $mappedActions = [];

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
     * @var ModuleEntity[]
     */
    protected $moduleEntities = [];

    /**
     * @var ModuleManager[]
     */
    protected $moduleManagers = [];

    /**
     * @var ModuleManager[]|null
     */
    protected $sortedModuleManagers = NULL;

    /**
     * @var ModuleManagerHelperLoader(]
     */
    protected $helperLoaders = [];

    /**
     * @var RequestContext
     */
    protected $initialRequestContext = NULL;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var FunctionQueue
     */
    protected $functionsQueueAfterCommitOrRollback;

    /**
     * @var RequestContextFactory
     */
    protected $reqCtxFactory;

    /**
     * @var CacheProvider
     */
    protected static $cacheProvider;

    /**
     * @var ParameterFactory
     */
    protected $parameterFactory;

    /**
     * @param object $entityName
     *
     * @return string
     */
    protected static function getEntityNameFromEntityObject ($entityName) {

        $class = get_class($entityName);
        $entityName = ($pos = strrpos($class, "\\")) ? substr($class, $pos + 1) : $class;

        return $entityName;
    }

    /**
     * @param RequestContext $initialRequestContext
     */
    public function setInitialRequestContext ($initialRequestContext) {

        $this->initialRequestContext = $initialRequestContext;

    }

    /**
     * @return RequestContext
     */
    public function getInitialRequestContext () {

        if (!$this->initialRequestContext) {
            return $this->getRequestContextFactory()->getNewRequestContext();
        }

        return $this->initialRequestContext;

    }

    /**
     *
     */
    protected function __construct () {

        $this->loadConstants();

        $this->routes = new RouteCollection();

        $this->initRequestContextFactory();

    }

    protected function loadConstants () {

        require __DIR__ . '/../../../config/constants.php';

    }

    /**
     * @return ApplicationContext
     */
    public static function getInstance () {

        if (!isset(static::$instance)) {
            static::$instance = new static();
        }

        return static::$instance;

    }

    public function addModuleManager (ModuleManager $moduleManager) {

        $this->moduleManagers[get_class($moduleManager)] = $moduleManager;

        $moduleEntityDefinitions = [];
        foreach ($moduleManager->getModuleEntitiesName($this) as $moduleEntityName) {
            $moduleEntityDefinitions[] = $this->getModuleEntityDefinition($moduleEntityName);
        }

        foreach ($moduleEntityDefinitions as $moduleEntityDefinition) {
            $moduleEntity = $moduleEntityDefinition instanceof AggregatedModuleEntityDefinition ?
                new AggregatedModuleEntity($this, $moduleEntityDefinition)
                : new DbModuleEntity($this, $moduleEntityDefinition);
            $moduleManager->addModuleEntity($moduleEntity);

            $entityName = $moduleEntity->getDefinition()->getEntityName();
            if (!array_key_exists($entityName, $this->moduleEntities)) {
                $this->moduleEntities[$entityName] = $moduleEntity;
                foreach ($moduleEntity->getDefinition()->getFilters() as $filter) {
                    $this->addFilter($filter);
                }

                // loading of calculated fields must be done after the load of model aspects
                /*
                $dbEntityName = $moduleEntity->getDefinition()->getDBEntityName();
                foreach ($moduleEntity->getDefinition()->getFields() as $fieldName => $calculatedField) {
                    $this->addCalculatedField($dbEntityName, $fieldName, $calculatedField);
                }
                */

            }

        }

        // loading of model aspect must be done after the definition of all Module Entities
        // so loadModuleEntities is called later by Application

        foreach ($moduleManager->createModuleFilters($this) as $filter) {
            $this->addFilter($filter);
        }

        foreach ($moduleManager->createRules($this) as $rule) {
            $this->addRule($rule);
        }

        foreach ($moduleManager->createActions($this) as $action) {
            $this->addAction($action);
        }

    }

    /**
     * @param string $helperName
     *
     * @return string
     */
    public function getHelperClassName ($helperName) {

        foreach ($this->getHelperLoader() as $helper) {
            if ($className = $helper::getHelperClassName($helperName)) {
                return $className;
            }
        }

        $moduleManagers = $this->getSortedModuleManagers();

        foreach ($moduleManagers as $moduleManager) {
            $baseClassName = $moduleManager->getNamespace();
            $className = $baseClassName . $helperName . 'Helper';
            if (class_exists($className)) {
                return $className;
            }
        }

    }

    /**
     * @return ModuleManagerHelperLoader[]
     */
    public function getHelperLoader () {

        if (!$this->helperLoaders) {

            foreach ($this->getSortedModuleManagers() as $moduleManager) {
                $baseClassName = $moduleManager->getNamespace();
                $className = $baseClassName . 'HelperLoader';
                if (class_exists($className)) {
                    $this->helperLoaders[] = $className;
                }
            }

        }

        return $this->helperLoaders;

    }

    /**
     * @param string $moduleEntityName
     *
     * @return ModuleEntityDefinition
     */
    protected function getModuleEntityDefinition ($moduleEntityName) {

        $cache = $this->getCacheProvider();

        $key = 'MODULE_ENTITY_DEFINITION_' . $moduleEntityName;
        $data = $cache->fetch($key);
        if ($data === false) {

            foreach ($this->application->getModuleManagers() as $moduleManager) {
                $moduleManagerClassName = get_class($moduleManager);
                $product = strstr($moduleManagerClassName, '\\', true);
                if ($product == 'Core') {
                    continue;
                }
                $baseClassName =
                    substr($moduleManager->getNamespace(), strlen($product));
                $className = $baseClassName . $moduleEntityName . 'Definition';
                if (ClassHelper::classExists($fullClassName = $product . $className)) {
                    $moduleEntityDefinition = new $fullClassName;
                    $cache->save($key, $moduleEntityDefinition);

                    return $moduleEntityDefinition;
                }
                if (ClassHelper::classExists($fullClassName = 'Core' . $className)) {
                    $moduleEntityDefinition = new $fullClassName;
                    $cache->save($key, $moduleEntityDefinition);

                    return $moduleEntityDefinition;
                }
            }

            throw new \RuntimeException(sprintf('ModuleEntityDefinition for %s not found', $moduleEntityName));
        }

        return $data;

    }

    /**
     * @return Application
     */
    public function getApplication () {

        return $this->application;

    }

    /**
     * @return \Core\Module\ModuleManager[]
     */
    public function getSortedModuleManagers () {

        if (!$this->sortedModuleManagers) {

            $this->sortedModuleManagers = $this->application->getModuleManagers();
            usort($this->sortedModuleManagers, function (ModuleManager $mm1, ModuleManager $mm2) {

                $mm1ClassName = get_class($mm1);
                $mm1Product = strstr($mm1ClassName, '\\', true);

                $mm2ClassName = get_class($mm2);
                $mm2Product = strstr($mm2ClassName, '\\', true);

                if ($mm1Product != $mm2Product) {
                    return $mm1Product == 'Core' ? 1 : -1;
                }

                $isMm1Magical = $mm1 instanceof MagicalModuleManager;
                $isMm2Magical = $mm2 instanceof MagicalModuleManager;

                if ($isMm1Magical xor $isMm2Magical) {
                    return $isMm1Magical ? -1 : 1;
                }

                return 0;

            });
        }

        return $this->sortedModuleManagers;

    }

    /**
     * @return Registry
     */
    protected function getNewRegistry () {

        $registry = new Registry($this->entityManager, $this);

        $this->entityManager->getEventManager()->addEventSubscriber($registry);

        return $registry;

    }

    /**
     * @param Filter $filter
     */
    public function addFilter (Filter $filter) {


        $filterName = $filter->getName();
        if (isset($this->filters[$filterName])) {
            throw new \RuntimeException(sprintf('Filter %s already defined', $filterName));
        }

        $this->filters[$filterName] = $filter;

    }

    /**
     * @param string     $entityName
     * @param string     $fieldName
     * @param Calculated $calculatedField
     */
    public function addCalculatedField ($entityName, $fieldName, Calculated $calculatedField) {

        $this->calculatedFields[$entityName][$fieldName] = $calculatedField;

    }

    /**
     * @param Rule $rule
     */
    public function addRule (Rule $rule) {

        $this->rules[] = $rule;

    }

    /**
     * @param Action $action
     */
    public function addAction (Action $action) {

        if (!array_key_exists($action->getModule(), $this->mappedActions)
            || !array_key_exists($action->getName(), $this->mappedActions[$action->getModule()])
        ) {
            $this->actions[] = $action;
            $this->mappedActions[$action->getModule()][$action->getName()] = $action;
        }
        else {
            throw new \RuntimeException('action already defined for this module and name (' . $action->getModule()
                                        . ',' . $action->getName() . ')');
        }

    }

    /**
     * @return Action[]
     */
    public function getActions () {

        return $this->actions;

    }

    public function isUnitTest () {

        return (php_sapi_name() == 'cli' && strpos($_SERVER['argv'][0], 'phpunit') !== false)
               || Request::createFromGlobals()->headers->get('user-agent') == static::UNIT_TESTS_USER_ARGENT;

    }

    /**
     * @param Application $application
     */
    public function setApplication (Application $application) {

        $this->application = $application;
        $this->product = strstr(get_class($application), '\\', true);

    }

    /**
     * @return ModuleManager[]
     */
    public function getModuleManagers () {

        return $this->moduleManagers;

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
     * @return \Core\Rule\Processor
     */
    public function getRuleProcessor () {

        return $this->ruleProcessor;

    }

    /**
     * @param \Core\Rule\Processor $ruleProcessor
     */
    public function setRuleProcessor (Processor $ruleProcessor) {

        $this->ruleProcessor = $ruleProcessor;

    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager (EntityManager $entityManager) {

        $this->entityManager = $entityManager;
        $entityManager->getConfiguration()->setSQLLogger($this->getSQLLogger());

        Registry::setApplicationContext($this);
        Registry::setEntityManager($entityManager);

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

        if (!$this->entityManager) {
            throw new \Exception;
        }

        return $this->entityManager->getClassMetadata($class);

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return $this->filters;

    }

    /**
     * @param string $name
     *
     * @return Filter
     */
    public function getFilterByName ($name) {

        if (!isset($this->filters[$name])) {
            throw new \RuntimeException(sprintf('Filter %s not found', $name));
        }

        return $this->filters[$name];

    }

    /**
     * @return Rule[]
     */
    public function getRules () {

        return $this->rules;

    }

    /**
     * @param $module
     * @param $name
     *
     * @return Action
     */
    public function getAction ($module, $name) {

        if (!isset($this->mappedActions[$module][$name])) {
            throw new \RuntimeException("Action $module/$name not found");
        }

        return $this->mappedActions[$module][$name];

    }

    /**
     * @param string $module
     * @param string $name
     * @param        $helper
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
    public function getHelper (Action $action, $name) {

        $module = $action->getModule();
        if (isset($this->helpers[$module][$name])) {
            return $this->helpers[$module][$name];
        }

        throw new \RuntimeException("Helper $module/$name not found");

    }

    /**
     * @param string   $path
     * @param Action   $action
     * @param string[] $defaultFields
     */
    public function addRoute ($path, Action $action, array $defaultFields = []) {

        $camelizedPath = str_replace(' ', '', ucwords(str_replace('/', ' ', $path)));
        $this->routes->add($camelizedPath, new Route($path, [
            'controller' => new Controller($action),
            'fields'     => $defaultFields,
        ]));

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

    /**
     * @param RequestContext $reqCtx
     * @param string         $moduleName
     * @param string         $actionName
     *
     * @return ActionContext
     */
    public function getActionContext (RequestContext $reqCtx, $moduleName, $actionName) {

        $refActionContext = new \ReflectionClass('\Core\Context\ActionContext');
        /**
         * @var ActionContext $actionContext
         */
        $actionContext = $refActionContext->newInstanceWithoutConstructor();
        $constructor = $refActionContext->getConstructor();
        $constructor->setAccessible(true);
        $constructor->invokeArgs($actionContext, [$reqCtx, $moduleName, $actionName]);
        $constructor->setAccessible(false);

        return $actionContext;

    }

    /**
     * @param Translator $translator
     */
    public function setTranslator (Translator $translator) {

        $this->translator = $translator;

    }

    /**
     * @return Translator
     */
    public function getTranslator () {

        if (is_null($this->translator)) {
            throw new \RuntimeException('qwe');
        }

        return $this->translator;

    }

    /**
     * @return ConfigManager
     */
    public function getConfigManager () {

        if (!isset($this->configManager)) {
            $configFiles = [];
            foreach ([ROOT_DIR . '/vendor/api/core/config/', ROOT_DIR . '/config/'] as $coreConfDir) {
                if (file_exists($configFile = $coreConfDir . 'default.yml')) {
                    $configFiles[] = $configFile;
                }
                if (file_exists($configFile = $coreConfDir . 'isDown.yml')) {
                    $configFiles[] = $configFile;
                }
                $configPath = $coreConfDir . $this->getEnvName() . '/config.yml';
                $dbPath = $coreConfDir . $this->getEnvName() . '/extra.yml';

                if (file_exists($configPath)) {
                    $configFiles[] = $configPath;
                }
                if (file_exists($dbPath)) {
                    $configFiles[] = $dbPath;
                }

            }

            $this->configManager = new ConfigManager($configFiles);
        }

        return $this->configManager;

    }

    public function getEnvType () {

        return $this->getEnvName() == 'prod' ? PROD_ENV : STAGE_ENV;
    }

    public function getEnvName () {

        if (getenv('ARCHIPAD_ENV')) {
            return getenv('ARCHIPAD_ENV');
        }
        else {
            if (isset($_SERVER['SERVER_NAME'])) {
                // php called by apache
                if ($_SERVER['SERVER_NAME'] == 'localhost'
                    || $_SERVER['SERVER_NAME'] == '127.0.0.1'
                    || substr($_SERVER['SERVER_NAME'], 0, 3) == '10.'
                    || substr($_SERVER['SERVER_NAME'], 0, 8) == '192.168.'
                    || strpos($_SERVER['REQUEST_URI'], 'api/archipad/www') !== false
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
                elseif (strpos(__DIR__, 'dev') !== false) {
                    return DEV_ENV;
                }
                elseif (strpos(__DIR__, 'stage') !== false) {
                    return STAGE_ENV;
                }
                else {
                    return PROD_ENV;
                }
            }
        }

    }

    /**
     * @return string
     */
    public function getProduct () {

        return $this->product;
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
     * @return FunctionQueue
     */
    public function getFunctionsQueueAfterCommitOrRollback () {

        if (!isset($this->functionsQueueAfterCommitOrRollback)) {
            $this->functionsQueueAfterCommitOrRollback = new FunctionQueue();
        }

        return $this->functionsQueueAfterCommitOrRollback;

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

    /**
     * @param FindQueryContext $findQueryContext
     */
    public function finalizeFindQueryContext (FindQueryContext $findQueryContext) {

        $moduleEntity = $this->getModuleEntity($findQueryContext->getEntity());
        $findQueryContext->setModuleEntity($moduleEntity);

    }

    /**
     * TODO: should we keep this api ?
     *
     * @param string $entityName
     *
     * @return ModuleEntity
     */
    protected function getModuleEntity ($entityName) {

        if (!isset($this->moduleEntities[$entityName])) {
            throw new \RuntimeException(sprintf('ModuleEntity %s not found', $entityName));
        }

        return $this->moduleEntities[$entityName];

    }

    /**
     * @param ModelAspect $modelAspect
     */
    public function finalizeModelAspect (ModelAspect $modelAspect) {

        $moduleEntity = $this->getModuleEntity($modelAspect->getModel());
        $modelAspect->setModuleEntity($moduleEntity);

    }

    /**
     * @param string|object $entityName
     * @param string        $fieldName
     *
     * @return bool
     */
    public function isCalculatedField ($entityName, $fieldName) {

        if (is_object($entityName)) {
            $entityName = static::getEntityNameFromEntityObject($entityName);
        }

        return isset($this->calculatedFields[$entityName][$fieldName]);

    }

    /**
     * @param string|object $entityName
     * @param string        $fieldName
     *
     * @return Calculated
     */
    public function getCalculatedField ($entityName, $fieldName) {

        if (is_object($entityName)) {
            $entityName = static::getEntityNameFromEntityObject($entityName);
        }

        if (!$this->isCalculatedField($entityName, $fieldName)) {
            throw new \RuntimeException(sprintf("Calculated field %s.%s not found", $entityName, $fieldName));
        }

        return $this->calculatedFields[$entityName][$fieldName];

    }

    /**
     * @param string|object $entityName
     *
     * @return Calculated[]
     */
    public function getCalculatedFields ($entityName) {

        if (is_object($entityName)) {
            $entityName = static::getEntityNameFromEntityObject($entityName);
        }

        return isset($this->calculatedFields[$entityName]) ? $this->calculatedFields[$entityName] : [];

    }

    /**
     * @param Serializer    $serializer
     * @param MagicalEntity $model
     */
    public function populateSerializerWithAggregatedModuleEntity (Serializer $serializer, MagicalEntity $model) {

        $exploded = explode('\\', get_class($model));
        $moduleEntity = $this->getModuleEntity(end($exploded));

        if (!($moduleEntity instanceof AggregatedModuleEntity)) {
            throw new \RuntimeException('AggregatedModuleEntity expected');
        }

        $serializer->setCurrentAggregatedModuleEntity($moduleEntity);

    }

    /**
     * @param string $entityName
     *
     * @return string
     */
    public function getRealModelClassName ($entityName) {

        $product = $this->getProduct();

        $class = '\\' . $product . '\Model\\' . $entityName;
        if (!class_exists($class)) {
            $class = '\Core\Model\\' . $entityName;
            if (!class_exists($class)) {
                throw new \RuntimeException(sprintf('entity %s not found', $entityName));
            }
        }

        return $class;

    }

    /**
     * @param string|object $classOrObject
     *
     * @return boolean
     */
    public function isEntity ($classOrObject) {

        if (is_object($classOrObject)) {
            $classOrObject = ClassUtils::getClass($classOrObject);
        }

        return !$this->entityManager->getMetadataFactory()->isTransient($classOrObject);

    }

    public function initRequestContextFactory () {

        $this->reqCtxFactory = new RequestContextFactory();

    }

    /**
     * @return RequestContextFactory
     */
    public function getRequestContextFactory () {

        return $this->reqCtxFactory;

    }

    /**
     * @return CacheProvider
     */
    public static function getCacheProvider () {

        if (!static::$cacheProvider) {

            try {

                $memCacheHost = getEnv('MEMCACHED_HOST');
                $memCachePort = getEnv('MEMCACHED_PORT');

                if (class_exists('Memcached')) {
                    $memcached = new \Memcached();
                    $memcached->addServer($memCacheHost, $memCachePort);
                    $memcachedCache = new MemcachedCache();
                    $memcachedCache->setMemcached($memcached);
                    static::$cacheProvider = $memcachedCache;
                }
                elseif (class_exists('Memcache')) {
                    $memcache = new \Memcache();
                    $memcache->connect($memCacheHost, $memCachePort);
                    $memcacheCache = new MemcacheCache();
                    $memcacheCache->setMemcache($memcache);
                    static::$cacheProvider = $memcacheCache;
                }
                else {
                    static::$cacheProvider = new ArrayCache();
                }
            } catch (\Exception $e) {
                static::$cacheProvider = new ArrayCache();
            }
        }

        return static::$cacheProvider;
    }

    /**
     * @return ParameterFactory
     */
    public function getParameterFactory() {

        if (!isset($this->parameterFactory)) {
            $this->parameterFactory = new ParameterFactory();
        }

        return $this->parameterFactory;

    }

}
