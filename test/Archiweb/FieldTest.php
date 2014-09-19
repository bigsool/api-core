<?php


namespace Archiweb;


class FieldTest extends \PHPUnit_Framework_TestCase {

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

        $filter = NULL;
        $field = new Field('entity', 'name', $filter);
        $this->assertSame($filter, $field->getFilter());

        $filter = $this->getMockBuilder('\Archiweb\Filter\Filter')
                       ->disableOriginalConstructor()
                       ->getMock();
        $field = new Field('entity', 'name', $filter);
        $this->assertSame($filter, $field->getFilter());

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
    public function testInvalidFilterType () {

        new Field('entity', 'name', new \stdClass());

    }

} 