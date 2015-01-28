<?php


namespace Core\Field;


use Core\TestCase;

class StarFieldTest extends TestCase {

    public function testName () {

        $field = new StarField('TestCompany');
        $this->assertSame('*', $field->getName());

    }

} 