<?php


namespace Core;


class RightsManagerTest extends TestCase {

    public function testAuth() {

        $auth = $this->getMockAuth();
        $rightsManager = $this->getMockRightsManager($auth);

        $this->assertSame($auth, $rightsManager->getAuth());

    }

}