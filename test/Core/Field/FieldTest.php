<?php


namespace Core\Field;


use Core\TestCase;

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