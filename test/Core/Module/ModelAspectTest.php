<?php


namespace Core\Module;


use Core\TestCase;

class ModelAspectTest extends TestCase {

    public function testAction () {

        $action = $this->getMockAction();
        $modelAspect = new ModelAspect('TestUser', 'TestUser', NULL, [], ['create' => $action], NULL);
        $this->assertSame($action, $modelAspect->getAction('create'));

    }

}