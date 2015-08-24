<?php


namespace Core\Error;

use Core\TestCase;

class LocalizedErrorTest extends TestCase {

    /**
     * @var LocalizedError
     */
    private $error;

    public function setUp () {

        parent::setUp();
        $this->error = new LocalizedError(201, "Id utilisateur invalide", "TestUser id invalid", 200, "userId");

    }

    public function testGetCode () {

        $this->assertEquals(201, $this->error->getCode());

    }

    public function testGetFrMessage () {

        $this->assertEquals("Id utilisateur invalide", $this->error->getFrMessage());

    }

    public function testGetEnMessage () {

        $this->assertEquals("TestUser id invalid", $this->error->getEnMessage());

    }

    public function testGetField () {

        $this->assertEquals("userId", $this->error->getField());

    }

    public function testGetParentCode () {

        $this->assertEquals(200, $this->error->getParentCode());

    }

}