<?php


namespace Archiweb;


use Archiweb\Model\Company;

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

        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
                   ->disableOriginalConstructor()
                   ->getMock();
        $ctx = new Context();
        $ctx->setEntityManager($em);
        $this->ctx = new ActionContext($ctx);

        $this->registry = new Registry($this->ctx);

    }

    public function testCreate () {

        $company = $this->registry->create('Company', array());

        $this->assertInstanceOf('\Archiweb\Model\Company', $company);

        $this->assertEquals(new Company(), $company);

    }

}