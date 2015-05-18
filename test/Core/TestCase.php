<?php


namespace Core;


use Core\Action\Action;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\QueryContext;
use Core\Context\RequestContext;
use Core\Context\SaveQueryContext;
use Core\Doctrine\Tools\EntityGenerator;
use Core\Error\Error;
use Core\Error\ErrorManager;
use Core\Error\FormattedError;
use Core\Expression\Expression;
use Core\Expression\ExpressionWithOperator;
use Core\Expression\KeyPath;
use Core\Expression\Value;
use Core\Field\Field;
use Core\Field\RelativeField;
use Core\Filter\Filter;
use Core\Module\MagicalModuleManager;
use Core\Module\ModelAspect;
use Core\Operator\CompareOperator;
use Core\Operator\LogicOperator;
use Core\Rule\Processor;
use Core\Rule\Rule;
use Doctrine\Common\Cache\ArrayCache;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;

Application::defineRootDir();

class TestCase extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        self::resetApplicationContext();

    }

    /**
     *
     */
    public static function resetApplicationContext () {

        $instanceProperty = (new \ReflectionClass('\Core\Context\ApplicationContext'))->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instanceProperty->setValue(NULL, NULL);
        $instanceProperty->setAccessible(false);

    }

    /**
     * @param ApplicationContext $appCtx
     */
    public static function resetDatabase (ApplicationContext $appCtx) {

        $prop = new \ReflectionProperty($appCtx, 'entityManager');
        $prop->setAccessible(true);

        $em = $prop->getValue($appCtx);

        $schemaTool = new SchemaTool($em);

        $cmf = $em->getMetadataFactory();
        $classes = $cmf->getAllMetadata();

        // TODO: instead of doing that i should backup and empty and copy it when i want to reset database
        $em->getConnection()->query('PRAGMA foreign_keys = OFF');
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($classes);
        $em->getConnection()->query('PRAGMA foreign_keys = ON');

    }

    /**
     * @return callable
     */
    public static function getCallable () {

        return function () {
        };

    }

    /**
     * @return ErrorManager
     */
    public function getMockErrorManager () {

        return $this->getMockBuilder('\Core\Error\ErrorManager')
                    ->disableOriginalConstructor()
                    ->getMockForAbstractClass();

    }

    /**
     * @return ModelAspect
     */
    public function getMockModelAspect () {

        return $this->getMockBuilder('\Core\Module\ModelAspect')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Auth
     */
    public function getMockAuth () {

        return $this->getMockBuilder('\Core\Auth')
                    ->getMock();

    }

    /**
     * @param callable[] $fnToOverride
     *
     * @return MagicalModuleManager
     */
    public function getMockMagicalModuleManager ($fnToOverride = []) {

        return $this->getMockBuilder('\Core\Module\MagicalModuleManager')
                    ->disableOriginalConstructor()
                    ->setMethods($fnToOverride)
                    ->getMockForAbstractClass();

    }

    /**
     * @return Application
     */
    public function getMockApplication () {

        return $this->getMockBuilder('\Core\Application')
                    ->getMock();

    }

    /**
     * @return QueryContext
     */
    public function getMockQueryContext () {

        return $this->getMockBuilder('\Core\Context\QueryContext')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return FindQueryContext
     */
    public function getMockFindQueryContext () {

        return $this->getMockBuilder('\Core\Context\FindQueryContext')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Registry
     */
    public function getMockRegistry () {

        return $this->getMockBuilder('\Core\Registry')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Rule
     */
    public function getMockRule () {

        return $this->getMockBuilder('\Core\Rule\Rule')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Filter
     */
    public function getMockFilter () {

        return $this->getMockBuilder('\Core\Filter\Filter')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Field
     */
    public function getMockField () {

        return $this->getMockBuilder('\Core\Field\Field')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Processor
     */
    public function getMockRuleProcessor () {

        return $this->getMock('\Core\Rule\Processor');

    }

    /**
     * @return EntityManager
     */
    public function getMockEntityManager () {

        return $this->getMockBuilder('\Doctrine\ORM\EntityManager')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return RequestContext
     */
    public function getMockRequestContext () {

        $requestContext = $this->getMockBuilder('\Core\Context\RequestContext')
                    ->disableOriginalConstructor()
                    ->getMock();

        $requestContext->method('getApplicationContext')->willReturn($this->getApplicationContext());
        return $requestContext;

    }

    /**
     * @param Auth $auth
     *
     * @return RightsManager
     */
    public function getMockRightsManager (Auth $auth) {

        return $this->getMockBuilder('\Core\RightsManager')
                    ->setConstructorArgs([$auth])
                    ->getMockForAbstractClass();

    }

    /**
     * @return ActionContext
     */
    public function getMockActionContext () {

        return $this->getMockBuilder('\Core\Context\ActionContext')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Parameter
     */
    public function getMockParameter () {

        return $this->getMockBuilder('\Core\Parameter\Parameter')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Expression
     */
    public function getMockExpression () {

        return $this->getMockBuilder('\Core\Expression\Expression')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return LogicOperator
     */
    public function getMockLogicOperator () {

        return $this->getMockBuilder('\Core\Operator\LogicOperator')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return CompareOperator
     */
    public function getMockCompareOperator () {

        return $this->getMockBuilder('\Core\Operator\CompareOperator')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Value
     */
    public function getMockValue () {

        return $this->getMockBuilder('\Core\Expression\Value')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return ExpressionWithOperator
     */
    public function getMockExpressionWithOperator () {

        $expression = $this->getMockBuilder('\Core\Expression\ExpressionWithOperator')
                           ->disableOriginalConstructor()
                           ->getMock();
        // if we need to return something else for getExpressions, improve this part
        $expression->method('getExpressions')->willReturn([]);

        return $expression;

    }

    /**
     * @return ApplicationContext
     */
    public function getMockApplicationContext () {

        return $this->getMockBuilder('\Core\Context\ApplicationContext')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Error
     */
    public function getMockError () {

        return $this->getMockBuilder('\Core\Error\Error')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return RequestContext
     */
    public function getRequestContext () {

        return new RequestContext();

    }

    /**
     * @return Action
     */
    public function getMockAction () {

        return $this->getMockBuilder('\Core\Action\Action')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return KeyPath
     */
    public function getMockKeyPath () {

        return $this->getMockBuilder('\Core\Expression\KeyPath')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return RelativeField
     */
    public function getMockRelativeField () {

        return $this->getMockBuilder('\Core\Field\RelativeField')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return FormattedError
     */
    public function getMockFormattedError () {

        return $this->getMockBuilder('\Core\Error\Formatted')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @param RequestContext $requestContext
     * @param string         $moduleName
     * @param string         $actionName
     *
     * @return ActionContext
     */
    public function getActionContext (RequestContext $requestContext = NULL, $moduleName = '', $actionName = '') {

        if (!$requestContext) {
            $requestContext = new RequestContext();
        }

        return $requestContext->getApplicationContext()->getActionContext($requestContext, $moduleName, $actionName);

    }

    /**
     * @param string         $entity
     * @param RequestContext $requestContext
     *
     * @return FindQueryContext
     */
    public function getFindQueryContext ($entity, RequestContext $requestContext = NULL) {

        return new FindQueryContext($entity, $requestContext);

    }

    /**
     * @param $model
     *
     * @return SaveQueryContext
     */
    public function getSaveQueryContext ($model) {

        return new SaveQueryContext($model);

    }

    /**
     * @param string $entity
     *
     * @return Registry
     */
    public function getRegistry ($entity = NULL) {

        $registry = $this->getApplicationContext()->getNewRegistry();

        if (isset($entity)) {
            $meth = new \ReflectionMethod($registry, 'addAliasForEntity');
            $meth->setAccessible(true);
            $meth->invokeArgs($registry, [$entity, lcfirst($entity)]);
        }

        return $registry;

    }

    /**
     * @param mixed $conn
     *
     * @return ApplicationContext
     * @throws \Doctrine\ORM\ORMException
     */
    public static function getApplicationContext ($conn = NULL) {

        $instanceProperty = (new \ReflectionClass('\Core\Context\ApplicationContext'))->getProperty('instance');
        $instanceProperty->setAccessible(true);
        $instance = $instanceProperty->getValue(NULL);
        $instanceProperty->setAccessible(false);

        if (!$instance) {

            $config =
                Setup::createYAMLMetadataConfiguration(array(__DIR__ . '/../yml'), true, __DIR__ . '/../proxy',
                                                       new ArrayCache());
            $config->addCustomHydrationMode('RestrictedObjectHydrator',
                                            'Core\Doctrine\Hydrator\RestrictedObjectHydrator');
            $config->addCustomHydrationMode('ArrayIdHydrator', 'Core\Doctrine\Hydrator\ArrayIdHydrator');
            $config->setSQLLogger(new DebugStack());
            $tmpDB = __DIR__ . '/../test.archiweb-proto.db.sqlite';

            if ($conn == NULL) {
                $conn = array(
                    'driver' => 'pdo_sqlite',
                    'path'   => $tmpDB,
                );
            }
            $em = EntityManager::create($conn, $config);
            self::generateEntities($em);
            $em->getConnection()->query('PRAGMA foreign_keys = ON');

            $appProperty = (new \ReflectionClass('\Core\Application'))->getProperty('instance');
            $appProperty->setAccessible(true);
            $appProperty->setValue(NULL);
            $appProperty->setAccessible(false);
            $app = Application::getInstance();
            $ctx = ApplicationContext::getInstance();
            $ctx->setApplication($app);
            $ruleMgr = new Processor();
            $ctx->setRuleProcessor($ruleMgr);
            $ctx->setEntityManager($em);

            //require __DIR__ . '/../../config/errors.php';

        }

        return ApplicationContext::getInstance();

    }

    public static function generateEntities (EntityManager $em) {

        $cmf = new DisconnectedClassMetadataFactory();
        $cmf->setEntityManager($em);

        foreach ($cmf->getAllMetadata() as $metadata) {

            $generator = new EntityGenerator();
            $generator->setFieldVisibility(EntityGenerator::FIELD_VISIBLE_PROTECTED);
            $generator->setBackupExisting(false);
            $generator->setGenerateAnnotations(true);
            $generator->setGenerateStubMethods(true);
            $generator->setRegenerateEntityIfExists(true);
            $generator->setUpdateEntityIfExists(true);
            $generator->generate(array($metadata), __DIR__ . '/..');

        }

    }

} 