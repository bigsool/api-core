<?php


namespace Core;


use Core\Action\Action;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\QueryContext;
use Core\Context\RequestContext;
use Core\Context\SaveQueryContext;
use Core\Error\Error;
use Core\Error\ErrorManager;
use Core\Expression\Expression;
use Core\Expression\ExpressionWithOperator;
use Core\Expression\KeyPath as ExpressionKeyPath;
use Core\Expression\Value;
use Core\Field\Field;
use Core\Field\KeyPath as FieldKeyPath;
use Core\Filter\Filter;
use Core\Module\MagicalModuleManager;
use Core\Module\ModelAspect;
use Core\Operator\CompareOperator;
use Core\Operator\LogicOperator;
use Core\Parameter\Parameter;
use Core\Rule\Rule;
use Core\Validation\AbstractConstraintsProvider;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;
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

        $em->getConnection()->query('PRAGMA foreign_keys = OFF');
        $schemaTool->dropDatabase();
        $schemaTool->createSchema($classes);
        $em->getConnection()->query('PRAGMA foreign_keys = ON');
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
     * @return RuleProcessor
     */
    public function getMockRuleProcessor () {

        return $this->getMock('\Core\RuleProcessor');

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

        return $this->getMockBuilder('\Core\Context\RequestContext')
                    ->disableOriginalConstructor()
                    ->getMock();

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
     * @return ExpressionKeyPath
     */
    public function getMockExpressionKeyPath () {

        return $this->getMockBuilder('\Core\Expression\KeyPath')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return FieldKeyPath
     */
    public function getMockFieldKeyPath () {

        return $this->getMockBuilder('\Core\Field\KeyPath')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return AbstractConstraintsProvider
     */
    public function getMockConstraintsProvider () {

        return $this->getMockBuilder('\Core\Validation\ConstraintsProvider')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return callable
     */
    public function getCallable () {

        return function () {
        };

    }

    /**
     * @return ActionContext
     */
    public function getActionContext () {

        return (new RequestContext())->getNewActionContext();

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
                Setup::createYAMLMetadataConfiguration(array(__DIR__ . "/../../model"), true,
                                                       __DIR__ . '/../../proxy/');
            $config->setSQLLogger(new DebugStack());
            $tmpDir = __DIR__ . '/../../doctrine/';
            $originalDb = $tmpDir . 'archiweb-proto.db.sqlite';
            $tmpDB = $tmpDir . 'tmp.archiweb-proto.db.sqlite';
            /*if (file_exists($originalDb)) {
                copy($originalDb, $tmpDB);
            }*/

            if ($conn == NULL) {
                $conn = array(
                    'driver' => 'pdo_sqlite',
                    'path'   => $tmpDB,
                );
            }
            $em = EntityManager::create($conn, $config);
            $em->getConnection()->query('PRAGMA foreign_keys = ON');

            $ctx = ApplicationContext::getInstance();
            $ruleMgr = new RuleProcessor();
            $ctx->setRuleProcessor($ruleMgr);
            $ctx->setEntityManager($em);

            require_once __DIR__ . '/../../config/errors.php';
            loadErrors($ctx->getErrorManager());

        }

        return ApplicationContext::getInstance();

    }

} 