<?php


namespace Core\Context;

use Core\Model\TestCompany;
use Core\Model\TestUser;
use Core\TestCase;

class SaveQueryContextTest extends TestCase {

    public static function setUpBeforeClass () {

        parent::setUpBeforeClass();

        // ApplicationContext will defined EntityManager which is necessary for the SaveQueryContext
        self::getApplicationContext();

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidModelType () {

        new SaveQueryContext('qwe');

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidModelClass () {

        new SaveQueryContext(new \stdClass());

    }

    public function testGetEntity () {

        $ctx = new SaveQueryContext(new TestUser());

        $this->assertSame('TestUser', $ctx->getEntity());

    }

} 