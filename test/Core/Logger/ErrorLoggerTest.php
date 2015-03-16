<?php


namespace Core\Logger;


use Core\TestCase;

class ErrorLoggerTest extends TestCase {

    public function testExceptionHandler () {

        $logger = new ErrorLogger();
        $this->assertInternalType('callable', $logger->getExceptionHandler());

    }

    public function testShutdownFunction () {

        $logger = new ErrorLogger();
        $this->assertInternalType('callable', $logger->getShutdownFunction());

    }

    public function testErrorHandler () {

        $logger = new ErrorLogger();
        $this->assertInternalType('callable', $logger->getErrorHandler());

    }

}