<?php


namespace Archiweb;


use Archiweb\Model\Company;
use Archiweb\Parameter\Parameter;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

class RegistryTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ActionContext
     */
    protected $ctx;

    public function setUp () {

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

        $ctx = new Context();
        $ctx->setEntityManager($em);
        $this->ctx = new ActionContext($ctx);

        $this->registry = new Registry($this->ctx);

    }

    public function testCreate () {

        $company = $this->registry->create('Company', array());
        $this->assertInstanceOf('\Archiweb\Model\Company', $company);
        $this->assertEquals(new Company(), $company);

        $company = $this->registry->create('Company', array('name' => $this->getParameterMock('company name', true)));
        $this->assertInstanceOf('\Archiweb\Model\Company', $company);
        $this->assertEquals('company name', $company->getName());

    }

    /**
     * @param mixed $value
     *
     * @return Parameter
     */
    protected function getParameterMock ($value, $isSafe) {

        $parameter = $this->getMockBuilder('\Archiweb\Parameter\Parameter')
                          ->disableOriginalConstructor()
                          ->getMock();
        $parameter->method('isSafe')->willReturn($isSafe);
        $parameter->method('getValue')->willReturn($value);

        return $parameter;

    }

    /**
     * @expectedException \Exception
     */
    public function testCreateWithFieldNotExists () {

        $this->registry->create('Company', array('qwe' => $this->getParameterMock('qwe', true)));

    }

    /**
     * @expectedException \Exception
     */
    public function testEntityNotFound () {

        $this->registry->create('Qwe', array());

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidParameterType () {

        $this->registry->create('Company', array('name' => 'qwe'));

    }

    public function testFind () {

        $qb = $this->registry->find('Company');
        $this->assertInstanceOf('\Doctrine\ORM\QueryBuilder', $qb);
        $dql = $qb->getDQL();
        $this->assertEquals('SELECT company FROM \Archiweb\Model\Company company', $dql);

    }

}