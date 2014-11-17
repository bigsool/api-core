<?php


namespace Core;

class ApplicationTest extends TestCase {

    public function testRun () {

        $app = new Application();

        $this->expectOutputString('{"jsonrpc":"2.0","error":{"code":2,"message":"invalid request","errors":[{"code":6,"message":"invalid protocol"}]},"id":null}');

        $app->run();

    }

} 