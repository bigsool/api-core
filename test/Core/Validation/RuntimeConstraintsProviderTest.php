<?php

namespace Core\Validation;

use Core\TestCase;

class RuntimeConstraintsProviderTest extends TestCase {

    /**
     * @expectedException \Exception
     */
    public function testInvalidKeyTypeConstructor () {

        new RuntimeConstraintsProvider([1 => []]);

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidConstraintsTypeConstructor () {

        new RuntimeConstraintsProvider(['qwe' => 'qwe']);

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidConstraintsObjectConstructor () {

        new RuntimeConstraintsProvider(['qwe' => [new \stdClass()]]);

    }

}