<?php


namespace Archiweb;


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

    public function testGetFilter () {

        $rule = NULL;
        $field = new Field('entity', 'name', $rule);
        $this->assertSame($rule, $field->getRule());

        $rule = $this->getMockRule();
        $field = new Field('entity', 'name', $rule);
        $this->assertSame($rule, $field->getRule());

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

    /**
     * @expectedException \Exception
     */
    public function testInvalidRuleType () {

        new Field('entity', 'name', new \stdClass());

    }

} 