<?php


namespace Archiweb;


use Archiweb\Action\Action;
use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Context\EntityManagerReceiver;
use Archiweb\Context\FindQueryContext;
use Archiweb\Context\QueryContext;
use Archiweb\Context\RequestContext;
use Archiweb\Context\SaveQueryContext;
use Archiweb\Expression\Expression;
use Archiweb\Expression\ExpressionWithOperator;
use Archiweb\Expression\KeyPath as ExpressionKeyPath;
use Archiweb\Expression\Value;
use Archiweb\Field\Field;
use Archiweb\Field\KeyPath as FieldKeyPath;
use Archiweb\Filter\Filter;
use Archiweb\Operator\CompareOperator;
use Archiweb\Operator\LogicOperator;
use Archiweb\Parameter\Parameter;
use Archiweb\Rule\Rule;
use Doctrine\DBAL\Logging\DebugStack;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;

class TestCase extends \PHPUnit_Framework_TestCase {

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

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($classes);
    }

    /**
     * @return QueryContext
     */
    public function getMockQueryContext () {

        return $this->getMockBuilder('\Archiweb\Context\QueryContext')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return FindQueryContext
     */
    public function getMockFindQueryContext () {

        return $this->getMockBuilder('\Archiweb\Context\FindQueryContext')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Registry
     */
    public function getMockRegistry () {

        return $this->getMockBuilder('\Archiweb\Registry')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Rule
     */
    public function getMockRule () {

        return $this->getMockBuilder('\Archiweb\Rule\Rule')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Filter
     */
    public function getMockFilter () {

        return $this->getMockBuilder('\Archiweb\Filter\Filter')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Field
     */
    public function getMockField () {

        return $this->getMockBuilder('\Archiweb\Field\Field')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return RuleProcessor
     */
    public function getMockRuleProcessor () {

        return $this->getMock('\Archiweb\RuleProcessor');

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

        return $this->getMockBuilder('\Archiweb\Context\RequestContext')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Parameter
     */
    public function getMockParameter () {

        return $this->getMockBuilder('\Archiweb\Parameter\Parameter')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Expression
     */
    public function getMockExpression () {

        return $this->getMockBuilder('\Archiweb\Expression\Expression')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return LogicOperator
     */
    public function getMockLogicOperator () {

        return $this->getMockBuilder('\Archiweb\Operator\LogicOperator')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return CompareOperator
     */
    public function getMockCompareOperator () {

        return $this->getMockBuilder('\Archiweb\Operator\CompareOperator')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return Value
     */
    public function getMockValue () {

        return $this->getMockBuilder('\Archiweb\Expression\Value')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return ExpressionWithOperator
     */
    public function getMockExpressionWithOperator () {

        $expression = $this->getMockBuilder('\Archiweb\Expression\ExpressionWithOperator')
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

        return $this->getMockBuilder('\Archiweb\Context\ApplicationContext')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return RequestContext
     */
    public function getRequestContext () {

        return new RequestContext($this->getApplicationContext());

    }

    /**
     * @return Action
     */
    public function getMockAction() {

        return $this->getMockBuilder('\Archiweb\Action\Action')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @param mixed $conn
     *
     * @return ApplicationContext
     * @throws \Doctrine\ORM\ORMException
     */
    public static function getApplicationContext ($conn = NULL) {

        $config =
            Setup::createYAMLMetadataConfiguration(array(__DIR__ . "/../../doctrine/model/yml"), true,
                                                   __DIR__ . '/../../src/');
        $config->setSQLLogger(new DebugStack());
        $tmpDir = sys_get_temp_dir();
        $originalDb = $tmpDir . '/archiweb-proto.db.sqlite';
        $tmpDB = tempnam($tmpDir, 'archiweb-proto.db.sqlite');
        if (file_exists($originalDb)) {
            copy($originalDb, $tmpDB);
        }

        if ($conn == NULL) {
            $conn = array(
                'driver' => 'pdo_sqlite',
                'path'   => $tmpDB,
            );
        }
        $em = EntityManager::create($conn, $config);
        $em->getConnection()->query('PRAGMA foreign_keys = ON');

        $ctx = new ApplicationContext();
        $ruleMgr = new RuleProcessor();
        $ctx->setRuleManager($ruleMgr);
        $ctx->setEntityManager($em);

        return $ctx;

    }

    /**
     * @return ExpressionKeyPath
     */
    public function getMockExpressionKeyPath () {

        return $this->getMockBuilder('\Archiweb\Expression\KeyPath')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return FieldKeyPath
     */
    public function getMockFieldKeyPath () {

        return $this->getMockBuilder('\Archiweb\Field\KeyPath')
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

        return (new RequestContext($this->getApplicationContext()))->getNewActionContext();

    }

    /**
     * @param string $entity
     * @param array  $fields
     * @param array  $filters
     *
     * @return FindQueryContext
     */
    public function getFindQueryContext ($entity, array $fields = [], array $filters = []) {

        return new FindQueryContext($this->getApplicationContext(), $entity, $fields, $filters);

    }

    /**
     * @param $model
     *
     * @return SaveQueryContext
     */
    public function getSaveQueryContext ($model) {

        return new SaveQueryContext($this->getApplicationContext(), $model);

    }

    /**
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

} 