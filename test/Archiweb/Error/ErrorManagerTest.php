<?php


namespace Archiweb\Error;

use Archiweb\TestCase;

class ErrorManagerTest extends TestCase {

    public function testAddError () {

        $error = $this->getMockError();
        $errorManager = new ErrorManager("fr");
        $errorManager->addError($error);
        $errors = $errorManager->getErrors();

        $this->assertTrue(in_array($error, $errors));

    }

   /* public function testAddDefinedError () {

        $error = $this->getMockError();
        ErrorManager::addDefinedError($error);
        $definedErrors = ErrorManager::getDefinedErrors();

        $this->assertTrue(in_array($error, $definedErrors));

    }*/

    public function testGetFormattedError () {

        $error1 = new Error(1,'message fr 1','message en 1','field1');

        $error10 = new Error(10,'message fr 10','message en 10','field10',1);

        $error100 = new Error(100,'message fr 100','message en 100','field100',10);
        $error101 = new Error(101,'message fr 101','message en 101','field101',10);

        $error1000 = new Error(1000,'message fr 1000','message en 1000','field1000',101);

        $error11 = new Error(11,'message fr 11','message en 11','field300',1);

        $errorManager = new ErrorManager("fr");
        $errorManager->addError($error1000);
        $errorManager->addError($error11);

        ErrorManager::addDefinedError($error1);
        ErrorManager::addDefinedError($error10);
        ErrorManager::addDefinedError($error100);
        ErrorManager::addDefinedError($error101);
        ErrorManager::addDefinedError($error1000);
        ErrorManager::addDefinedError($error11);

        $formattedError = $errorManager->getFormattedError();

        $this->assertEquals($formattedError->getCode(),1);
        $this->assertCount(2,$formattedError->getChildErrors());

        $formattedChildErrors = $formattedError->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(),10);
        $this->assertEquals($formattedChildErrors[1]->getCode(),11);

        $formattedChildErrors = $formattedChildErrors[0]->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(),101);
        $this->assertCount(1,$formattedChildErrors);

        $formattedChildErrors = $formattedChildErrors[0]->getChildErrors();

        $this->assertEquals($formattedChildErrors[0]->getCode(),1000);
        $this->assertCount(1,$formattedChildErrors);

    }

}