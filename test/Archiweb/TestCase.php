<?php


namespace Archiweb;


use Archiweb\Context\ApplicationContext;
use Archiweb\Context\QueryContext;
use Archiweb\Filter\Filter;
use Archiweb\Rule\Rule;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class TestCase extends \PHPUnit_Framework_TestCase {

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
    public function getMockFilter() {

        return $this->getMockBuilder('\Archiweb\Filter\Filter')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

} 