<?php


namespace Archiweb\Error;

use Archiweb\TestCase;
use Archiweb\Error\Error;
use Archiweb\Error\formattedError;

class FormattedErrorTest extends TestCase {


    private $formattedError;
    private $childErrors;

    public function setUp () {

        parent::setUp();

        FormattedError::setLang("fr");

        $error = $this->getMockError();
        $error->method('getFrMessage')->willReturn('echec d\'authentification');
        $error->method('getEnMessage')->willReturn('login fail');
        $error->method('getCode')->willReturn(200);
        $error->method('getField')->willReturn("userId");

        $this->formattedError = new FormattedError($error);

        $error = $this->getMockError();
        $error->method('getFrMessage')->willReturn('Id utilisateur invalide');
        $error->method('getEnMessage')->willReturn('User id invalid');
        $error->method('getCode')->willReturn(201);
        $error->method('getParentCode')->willReturn(200);
        $error->method('getField')->willReturn("userId");

        $this->childErrors [] = new FormattedError($error);

    }

    public function testAddChildError()  {

        $this->formattedError->addChildError($this->childErrors[0]);
        $this->assertTrue(in_array($this->childErrors[0], $this->formattedError->getChildErrors()));

    }

    public function testGetCode()  {

        $this->assertEquals(200, $this->formattedError->getCode());

    }

    public function testField()  {

        $this->assertEquals("userId", $this->formattedError->getField());

    }

    public function testGetMessage()  {

        $error = $this->getMockError();
        $error->method('getFrMessage')->willReturn('echec d\'authentification');
        $error->method('getEnMessage')->willReturn('login fail');

        FormattedError::setLang("fr");
        $formattedError = new FormattedError($error);
        $this->assertEquals('echec d\'authentification', $formattedError->getMessage());

        FormattedError::setLang("en");
        $formattedError = new FormattedError($error);
        $this->assertEquals('login fail', $formattedError->getMessage());


    }




}