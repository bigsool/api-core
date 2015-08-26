<?php


namespace Core\Field;


use Core\Context\FindQueryContext;
use Core\Model\TestUser;
use Core\TestCase;

class CalculatedFieldTest extends TestCase {

    public static function setUpBeforeClass () {

        $appCtx = self::getApplicationContext();

        $appCtx->addCalculatedField('TestUser', 'fullName', new CalculatedField(function () {

            return json_encode(func_get_args());

        }, ['lastName', 'firstName']));

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidKeyPath () {

        (new CalculatedField($this->getCallable()))->setValue('qweé"\'é" -"');

    }

    /**
     * @expectedException \Exception
     */
    public function testCalculatedFieldNotFound () {

        $calculatedField = new CalculatedField($this->getCallable());
        $arr = [];
        $calculatedField->execute($arr);

    }

    public function testShouldResolveForAWhere () {

        $this->assertFalse((new CalculatedField($this->getCallable()))->shouldResolveForAWhere());

    }

    public function testAlias () {

        $alias = 'alias';
        $field = new CalculatedField($this->getCallable());
        $field->setAlias($alias);
        $this->assertSame($alias, $field->getAlias());

    }

    public function testValue () {

        $value = 'value';
        $field = new CalculatedField($this->getCallable());
        $field->setValue($value);
        $this->assertSame($value, $field->getValue());

    }

    public function testGetFinalFields () {

        $calculatedField = new CalculatedField(function () {

            return json_encode(func_get_args());

        }, ['name', 'lastName']);
        $this->getApplicationContext()->addCalculatedField('TestUser', 'fullName2', $calculatedField);

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

        $calculatedField = $this->getApplicationContext()->getCalculatedField('TestUser', 'fullName');
        $ctx = new FindQueryContext('TestUser');
        $registry = $this->getRegistry();
        $fields = $calculatedField->resolve($registry, $ctx);
        $this->assertCount(0, $fields);

    }

    public function testExecute () {

        $calculatedField = $this->getApplicationContext()->getCalculatedField('TestUser', 'fullName');
        $ctx = new FindQueryContext('TestUser');
        $registry = $this->getRegistry();
        $calculatedField->resolve($registry, $ctx);
        $data = ['lastName' => 'my name', 'firstName' => 'my firstname'];
        $user = new TestUser();
        $user->setLastName('my name');
        $user->setFirstname('my firstname');
        $this->assertSame(json_encode(array_values($data)), $calculatedField->execute($user));

    }

}