<?php


namespace Core\Error;

use Core\TestCase;

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
        $error->method('getEnMessage')->willReturn('TestUser id invalid');
        $error->method('getCode')->willReturn(201);
        $error->method('getParentCode')->willReturn(200);
        $error->method('getField')->willReturn("userId");

        $this->childErrors [] = new FormattedError($error);

        /*$error = $this->getMockError();
        $error->method('getFrMessage')->willReturn('Mot de passe invalide');
        $error->method('getEnMessage')->willReturn('Password invalid');
        $error->method('getCode')->willReturn(202);
        $error->method('getParentCode')->willReturn(200);
        $error->method('getField')->willReturn("password");*/
        $error = [
            'frMessage'  => 'Mot de passe invalide',
            'enMessage'  => 'Password invalid',
            'code'       => 202,
            'parentCode' => 200,
            'field'      => 'password',
        ];

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

        $formattedError = new FormattedError(['code' => 0, 'message' => 'qwe']);
        $this->assertEquals('qwe', $formattedError->getMessage());

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

    /**
     * @expectedException \Exception
     */
    public function testErrorCodeNotFound () {

        new FormattedError([]);

    }

    /**
     * @expectedException \Exception
     */
    public function testErrorMessageNotFound () {

        new FormattedError(['code' => 0]);

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidConstructor () {

        new FormattedError(201);

    }

    public function testFormattedErrorWithChildInArray () {

        $errorArray = ['code'    => 123,
                       'message' => 'message 123',
                       'errors'  => [
                           ['code'    => 456,
                            'message' => 'message 456',
                            'errors'  => [
                                ['code' => 789, 'message' => 'message 789'],
                                ['code' => 101112, 'message' => 'message 101112']
                            ]
                           ],
                           ['code' => 131415, 'message' => 'message 131415']
                       ]
        ];

        $formattedError = new FormattedError($errorArray);
        $this->assertSame($errorArray, $formattedError->toArray());

    }

    public function testFormattedErrorWithSerializedMessage () {

        $errorArray = ['code'             => 123,
                       'localizedMessage' => 'message 123'
        ];

        $errorExpected = ['code'             => 123,
                          'message'          => 'message 123',
                          'localizedMessage' => 'message 123'
        ];
        FormattedError::setLang("fr");
        $formattedError = new FormattedError($errorArray);
        $this->assertSame($errorExpected, $formattedError->toArray());

    }

}