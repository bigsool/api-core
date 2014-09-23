<?php


namespace Archiweb;


use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Context\QueryContext;
use Archiweb\Context\RequestContext;
use Archiweb\Filter\Filter;
use Archiweb\Rule\Rule;
use Doctrine\ORM\EntityManager;
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
     * @return Registry
     */
    public function getMockRegistry () {

        return $this->getMock('\Archiweb\Registry');

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
    public function getMockRequestContext() {

        return $this->getMockBuilder('\Archiweb\Context\RequestContext')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @return RequestContext
     */
    public function getMockParameter() {

        return $this->getMockBuilder('\Archiweb\Parameter\Parameter')
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
     * @return ActionContext
     */
    public function getActionContext() {

        return (new RequestContext($this->getApplicationContext()))->getNewActionContext();

    }

    /**
     * @return QueryContext
     */
    public function getQueryContext() {

        return new QueryContext($this->getApplicationContext());

    }

    /**
     * @return ApplicationContext
     */
    public function getApplicationContext () {

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

} 