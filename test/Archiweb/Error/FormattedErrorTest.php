<?php


namespace Archiweb\Error;

use Archiweb\TestCase;

class FormattedErrorTest extends TestCase {

    private $formattedError;

    private $childErrors;

    public function setUp () {

        parent::setUp();

        FormattedError::setLang("fr");

        $error = $this->getMockError();
        $error->method('getFrMessage')->willReturn('echec authentification');
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

        $error = $this->getMockError();
        $error->method('getFrMessage')->willReturn('Mot de passe invalide');
        $error->method('getEnMessage')->willReturn('Password invalid');
        $error->method('getCode')->willReturn(202);
        $error->method('getParentCode')->willReturn(200);
        $error->method('getField')->willReturn("password");

        $this->childErrors [] = new FormattedError($error);

    }

    public function testAddChildError () {

        $this->formattedError->addChildError($this->childErrors[0]);
        $this->assertTrue(in_array($this->childErrors[0], $this->formattedError->getChildErrors()));

    }

    public function testGetCode () {

        $this->assertEquals(200, $this->formattedError->getCode());

    }

    public function testField () {

        $this->assertEquals("userId", $this->formattedError->getField());

    }

    public function testGetMessage () {

        $error = $this->getMockError();
        $error->method('getFrMessage')->willReturn('echec authentification');
        $error->method('getEnMessage')->willReturn('login fail');

        FormattedError::setLang("fr");
        $formattedError = new FormattedError($error);
        $this->assertEquals('echec authentification', $formattedError->getMessage());

        FormattedError::setLang("en");
        $formattedError = new FormattedError($error);
        $this->assertEquals('login fail', $formattedError->getMessage());

    }

    public function testToString () {

        $this->formattedError->addChildError($this->childErrors[0]);
        $this->formattedError->addChildError($this->childErrors[1]);
        $childErrors = $this->formattedError->getChildErrors();

        $tab = ["code"    => $this->formattedError->getCode(),
                "message" => $this->formattedError->getMessage(),
                "field"   => $this->formattedError->getField(),
                "errors"  => [
                    [
                        "code"    => $childErrors[0]->getCode(),
                        "message" => $childErrors[0]->getMessage(),
                        "field"   => $childErrors[0]->getField(),
                    ],
                    [
                        "code"    => $childErrors[1]->getCode(),
                        "message" => $childErrors[1]->getMessage(),
                        "field"   => $childErrors[1]->getField(),
                    ]
                ]
        ];

        $this->assertEquals($this->formattedError, json_encode($tab));

    }

}