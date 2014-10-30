<?php


namespace Archiweb;

class ApplicationTest extends TestCase {

    public function testRun () {

        $app = new Application();
        $app->run();

        // TODO : replace this test by a test of the output
        $this->assertTrue(true);

    }

} 