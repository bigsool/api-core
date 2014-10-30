<?php


namespace Archiweb;

class ApplicationTest extends TestCase {

    public function testRun () {

        $app = new Application();

        $this->expectOutputString('{"code":2,"message":"invalid request","errors":[{"code":5,"message":"invalid client"}]}');

        $app->run();

    }

} 