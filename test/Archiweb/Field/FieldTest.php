<?php


namespace Archiweb\Field;


use Archiweb\TestCase;

class FieldTest extends TestCase {

    public function testGetEntity () {

        $entity = 'entity';
        $field = new Field($entity, 'name');
        $this->assertSame($entity, $field->getEntity());

    }

    public function testGetName () {

        $name = 'name';
        $field = new Field('entity', $name);
        $this->assertSame($name, $field->getName());

    }

    public function testRules () {

        $field = new Field('entity', 'name');
        $this->assertSame([], $field->getRules());

        $rule = $this->getMockRule();
        $field = new Field('entity', 'name');
        $field->addRule($rule);

        $this->assertSame([$rule], $field->getRules());

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidEntityType () {

        new Field([], 'name');

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidNameType () {

        new Field('entity', []);

    }

} 