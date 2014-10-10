<?php


namespace Archiweb\Error;

use Archiweb\TestCase;

class ErrorTest extends TestCase {

    private $error;

    public function setUp () {

        parent::setUp();
        $this->error = new Error(201, "Id utilisateur invalide", "User id invalid", "userId", 200);

    }

    public function testGetCode () {

        $this->assertEquals(201, $this->error->getCode());

    }

    public function testGetFrMessage () {

        $this->assertEquals("Id utilisateur invalide", $this->error->getFrMessage());

    }

    public function testGetEnMessage () {

        $this->assertEquals("User id invalid", $this->error->getEnMessage());

    }

    public function testGetField () {

        $this->assertEquals("userId", $this->error->getField());

    }

    public function testGetParentCode () {

        $this->assertEquals(200, $this->error->getParentCode());

    }

}