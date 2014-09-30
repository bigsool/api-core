<?php


namespace Archiweb;


use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Context\EntityManagerReceiver;
use Archiweb\Context\FindQueryContext;
use Archiweb\Context\QueryContext;
use Archiweb\Context\RequestContext;
use Archiweb\Context\SaveQueryContext;
use Archiweb\Expression\Expression;
use Archiweb\Expression\ExpressionWithOperator;
use Archiweb\Expression\KeyPath;
use Archiweb\Filter\Filter;
use Archiweb\Operator\LogicOperator;
use Archiweb\Parameter\Parameter;
use Archiweb\Rule\Rule;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\Setup;

class TestCase extends \PHPUnit_Framework_TestCase {

    /**
     * @return QueryContext
     */
    public function getMockQueryContext () {

        return $this->getMockBuilder('\Archiweb\Context\QueryContext')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return QueryContext
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

        return $this->getMockBuilder('\Archiweb\Field')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return RuleManager
     */
    public function getMockRuleManager () {

        return $this->getMock('\Archiweb\RuleManager');

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
     * @return ExpressionWithOperator
     */
    public function getMockExpressionWithOperator () {

        return $this->getMockBuilder('\Archiweb\Expression\ExpressionWithOperator')
                    ->disableOriginalConstructor()
                    ->getMock();

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
     * @return KeyPath
     */
    public function getMockKeyPath() {

        return $this->getMockBuilder('\Archiweb\Expression\KeyPath')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return ApplicationContext
     */
    public static function getApplicationContext () {

        $config =
            Setup::createYAMLMetadataConfiguration(array(__DIR__ . "/../../doctrine/model/yml"), true,
                                                   __DIR__ . '/../../src/');
        $tmpDir = sys_get_temp_dir();
        $originalDb = $tmpDir . '/archiweb-proto.db.sqlite';
        $tmpDB = tempnam($tmpDir, 'archiweb-proto.db.sqlite');
        if (file_exists($originalDb)) {
            copy($originalDb, $tmpDB);
        }

        $conn = array(
            'driver' => 'pdo_sqlite',
            'path'   => $tmpDB,
        );
        $em = EntityManager::create($conn, $config);

        // this query activate the foreign key in sqlite
        $em->getConnection()->query('PRAGMA foreign_keys = ON');

        $ctx = new ApplicationContext();
        $ruleMgr = new RuleManager();
        $ctx->setRuleManager($ruleMgr);
        $ctx->setEntityManager($em);

        return $ctx;

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
    public function getRegistry () {

        return $this->getApplicationContext()->getNewRegistry();

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

        $schemaTool->dropDatabase();
        $schemaTool->createSchema($classes);
    }

} 