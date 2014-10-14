<?php


namespace Archiweb\Error;

use Archiweb\TestCase;

class ErrorManagerTest extends TestCase {

    /**
     * @var Error
     */
    protected static $errMock1;

    /**
     * @var Error
     */
    protected static $error1;

    /**
     * @var Error
     */
    protected static $error10;

    /**
     * @var Error
     */
    protected static $error100;

    /**
     * @var Error
     */
    protected static $error101;

    /**
     * @var Error
     */
    protected static $error1000;

    /**
     * @var Error
     */
    protected static $error11;

    public static function setUpBeforeClass () {

        define('__TEST__ERR_MOCK_1', rand());
        ErrorManager::addDefinedError(
            self::$errMock1 = new Error(__TEST__ERR_MOCK_1, '__TEST__ERR_MOCK_1', '__TEST__ERR_MOCK_1'));

        ErrorManager::addDefinedError(self::$error1 = new Error(1, 'message fr 1', 'message en 1', null,'field1'));

        ErrorManager::addDefinedError(self::$error10 = new Error(10, 'message fr 10', 'message en 10', 1, 'field10'));

        ErrorManager::addDefinedError(
            self::$error100 = new Error(100, 'message fr 100', 'message en 100', 10, 'field100'));
        ErrorManager::addDefinedError(
            self::$error101 = new Error(101, 'message fr 101', 'message en 101', 10, 'field101'));

        ErrorManager::addDefinedError(
            self::$error1000 = new Error(1000, 'message fr 1000', 'message en 1000', 101, 'field1000'));

        ErrorManager::addDefinedError(self::$error11 = new Error(11, 'message fr 11', 'message en 11', 1, 'field300'));

    }

    public function testAddError () {

        $errorManager = new ErrorManager("fr");
        $this->assertCount(0, $errorManager->getErrors());

        $errorManager->addError(__TEST__ERR_MOCK_1);
        $errors = $errorManager->getErrors();
        $this->assertCount(1, $errors);
        $this->assertContains(self::$errMock1, $errors);

    }

    /* public function testAddDefinedError () {

         $error = $this->getMockError();
         ErrorManager::addDefinedError($error);
         $definedErrors = ErrorManager::getDefinedErrors();

         $this->assertTrue(in_array($error, $definedErrors));

     }*/

    public function testGetFormattedError () {

        $errorManager = new ErrorManager("fr");

        $errorManager->addError(self::$error1000->getCode());
        $formattedError = $errorManager->getFormattedError();
        $this->assertEquals($formattedError->getCode(), 1);
        $this->assertCount(1, $formattedError->getChildErrors());

        $errorManager->addError(self::$error11->getCode());
        $formattedError = $errorManager->getFormattedError();
        $this->assertEquals($formattedError->getCode(), 1);
        $this->assertCount(2, $formattedError->getChildErrors());

        $formattedChildErrors = $formattedError->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(), 10);
        $this->assertEquals($formattedChildErrors[1]->getCode(), 11);

        $formattedChildErrors = $formattedChildErrors[0]->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(), 101);
        $this->assertCount(1, $formattedChildErrors);

        $formattedChildErrors = $formattedChildErrors[0]->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(), 1000);
        $this->assertCount(1, $formattedChildErrors);



        $errorManager = new ErrorManager("fr");
        $errorManager->addError(self::$error1->getCode());
        $formattedError = $errorManager->getFormattedError();
        $this->assertEquals($formattedError->getCode(), 1);
        $this->assertCount(0, $formattedError->getChildErrors());

        $errorManager = new ErrorManager("fr");
        $errorManager->addError(self::$error1->getCode(),"fieldModified");
        $formattedError = $errorManager->getFormattedError();
        $this->assertEquals($formattedError->getField(),"fieldModified");


    }

    public function testGetDefinedError () {

        $errorManager = new ErrorManager("fr");
        $definedError = $errorManager->getDefinedError(self::$error1 ->getCode());
        $this->assertSame(self::$error1,$definedError);

    }

}