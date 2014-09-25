<?php


namespace Archiweb;


class StarFieldTest extends TestCase {

    public function testName () {

        $field = new StarField('Company');
        $this->assertSame('*', $field->getName());

    }

} 