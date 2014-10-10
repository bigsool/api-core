<?php


namespace Archiweb\Error;

use Archiweb\TestCase;
use Archiweb\Error\Error;
use Archiweb\Error\ErrorManager;

class ErrorManagerTest extends TestCase {

    public function testAddError () {

        $error = $this->getMockError();
        $errorManager = new ErrorManager("fr");
        $errorManager->addError($error);
        $errors = $errorManager->getErrors();

        $this->assertTrue(in_array($error,$errors));

    }

    public function testAddDefinedError() {

        $error = $this->getMockError();
        ErrorManager::addDefinedError($error);
        $definedErrors = ErrorManager::getDefinedErrors();

        $this->assertTrue(in_array($error,$definedErrors));

    }

}