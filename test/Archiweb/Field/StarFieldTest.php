<?php


namespace Archiweb\Field;


use Archiweb\TestCase;

class StarFieldTest extends TestCase {

    public function testName () {

        $field = new StarField('Company');
        $this->assertSame('*', $field->getName());

    }

} 