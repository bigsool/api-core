<?php


namespace Core\Field;


use Core\Context\FindQueryContext;
use Core\TestCase;

class CalculatedFieldTest extends TestCase {

    public static function tearDownAfterClass () {

        $refPropCalculatedFields = new \ReflectionProperty('\Core\Field\CalculatedField', 'calculatedFields');
        $refPropCalculatedFields->setAccessible(true);
        $refPropCalculatedFields->setValue([]);

    }

    public static function setUpBeforeClass () {

        CalculatedField::create('TestUser', 'fullName', function () {

            return json_encode(func_get_args());

        }, ['name', 'lastName']);

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidKeyPath () {

        new CalculatedField('qweé"\'é" -"');

    }

    /**
     * @expectedException \Exception
     */
    public function testCalculatedFieldNotFound () {

        $calculatedField = new CalculatedField('qwe');
        $arr = [];
        $calculatedField->exec($arr);

    }

    public function testShouldResolveForAWhere () {

        $this->assertFalse((new CalculatedField('qwe'))->shouldResolveForAWhere());

    }

    /**
     * @expectedException \Exception
     */
    public function testCalculatedFieldNotFoundInExec () {

        $calculatedField = new CalculatedField('qwe');
        $ctx = new FindQueryContext('testUser');
        $registry = $this->getRegistry();
        $calculatedField->getFinalFields($registry, $ctx);

    }

    public function testAlias () {

        $alias = 'alias';
        $field = new CalculatedField('qwe');
        $field->setAlias($alias);
        $this->assertSame($alias, $field->getAlias());

    }

    public function testValue () {

        $value = 'value';
        $field = new CalculatedField($value);
        $this->assertSame($value, $field->getValue());

    }

    public function testGetFinalFields () {

        CalculatedField::create('TestUser', 'fullName2', function () {

            return json_encode(func_get_args());

        }, ['name', 'lastName']);

        $calculatedField = new CalculatedField('fullName2');
        $ctx = new FindQueryContext('testUser');
        $registry = $this->getRegistry();
        $fields = $calculatedField->getFinalFields($registry, $ctx);
        $this->assertCount(2, $fields);

        $hasName = $hasLastName = false;

        foreach ($fields as $field) {
            switch ($field->getValue()) {
                case 'name':
                    $hasName = true;
                    break;
                case 'lastName':
                    $hasLastName = true;
                    break;
                default:
                    $this->fail('Unexpected field');
            }
        }

        $this->assertTrue($hasName, 'Field Name not found');
        $this->assertTrue($hasLastName, 'Field LastName not found');

    }

    public function testResolve () {

        $calculatedField = new CalculatedField('fullName');
        $ctx = new FindQueryContext('testUser');
        $registry = $this->getRegistry();
        $fields = $calculatedField->resolve($registry, $ctx);
        $this->assertCount(0, $fields);

    }

    public function testExec () {

        $calculatedField = new CalculatedField('fullName');
        $ctx = new FindQueryContext('testUser');
        $registry = $this->getRegistry();
        $calculatedField->resolve($registry, $ctx);
        $data = ['name' => 'my name', 'lastName' => 'my lastName'];
        $this->assertSame(json_encode(array_values($data)), $calculatedField->exec($data));

    }

}