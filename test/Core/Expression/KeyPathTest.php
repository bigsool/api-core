<?php


namespace Core\Expression;

use Core\Model\TestCompany;
use Core\TestCase;

class KeyPathTest extends TestCase {

    /**
     *
     */
    public function testGetValue () {

        $param = new KeyPath('user.company.storage');
        $this->assertEquals('user.company.storage', $param->getValue());

    }

    /**
     * @expectedException \Exception
     */
    public function testField () {

        (new KeyPath('user.company.storage'))->getField();

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidFormat () {

        new KeyPath('qwe/qwe');
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidType () {

        new KeyPath(new \stdClass());
    }

    /**
     *
     */
    public function testResolveField () {

        $registry = $this->getRegistry('TestCompany');
        $context = $this->getFindQueryContext('TestCompany');

        $param = 'owner.company.storage.url';

        $param1 = new KeyPath($param);
        $resolve1 = $param1->resolve($registry, $context);

        $this->assertEquals('testCompanyOwnerCompanyStorage.url', $resolve1);

        $registry = $this->getRegistry('TestCompany');
        $param2 = new KeyPath('name');
        $resolve2 = $param2->resolve($registry, $context);

        $this->assertEquals('testCompany.name', $resolve2);

        $param3 = new KeyPath('owner.company.storage');
        $resolve3 = $param3->resolve($registry, $context);

        $this->assertEquals('testCompanyOwnerCompany.storage', $resolve3);
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidContext () {

        $registry = $this->getRegistry();
        $context = $this->getSaveQueryContext(new TestCompany());

        (new KeyPath('owner.company.storage.url'))->resolve($registry, $context);
    }

    /**
     * @expectedException \Exception
     */
    public function testFieldIsFieldNotEntity () {

        $registry = $this->getRegistry();
        $context = $this->getFindQueryContext('TestCompany');

        (new KeyPath('owner.name.company.storage.url'))->resolve($registry, $context);
    }

    /**
     * @expectedException \Exception
     */
    public function testFieldNotFound () {

        $registry = $this->getRegistry();
        $context = $this->getFindQueryContext('TestCompany');

        (new KeyPath('owner.qweqwe'))->resolve($registry, $context);
    }

    /**
     * @expectedException \Exception
     */
    public function testStarFieldNotAtTheEnd () {

        $registry = $this->getRegistry();
        $context = $this->getFindQueryContext('TestCompany');

        (new KeyPath('owner.*.name'))->resolve($registry, $context);
    }

    /**
     * @expectedException \Exception
     */
    public function testAliasNotFound () {

        $registry = $this->getRegistry('TestCompany');
        $context = $this->getFindQueryContext('TestUser');

        $param = '*';

        (new KeyPath($param))->resolve($registry, $context);

    }

    /**
     * @expectedException \Exception
     */
    public function testMoreThanOneAliasFound () {

        $registry = $this->getRegistry('TestCompany');
        $context = $this->getFindQueryContext('TestCompany');

        (new KeyPath('owner'))->resolve($registry, $context);
        (new KeyPath('users'))->resolve($registry, $context);

        $keyPath = new KeyPath('name');
        $keyPath->setRootEntity('TestUser');
        $keyPath->resolve($registry, $context);

    }

} 