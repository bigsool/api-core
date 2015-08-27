<?php


namespace Core\Expression;

use Core\Model\TestCompany;
use Core\Model\TestUser;
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

        $registry = $this->getRegistry('TestUser');
        $context = $this->getFindQueryContext('TestUser');

        $param = 'credential.loginHistories.date';

        $param1 = new KeyPath($param);
        $resolve1 = $param1->resolve($registry, $context);

        $this->assertEquals('testUserCredentialLoginHistories.date', $resolve1);

        $registry = $this->getRegistry('TestUser');
        $param2 = new KeyPath('firstName');
        $resolve2 = $param2->resolve($registry, $context);

        $this->assertEquals('testUser.firstName', $resolve2);

        $param3 = new KeyPath('credential.loginHistories');
        $resolve3 = $param3->resolve($registry, $context);

        $this->assertEquals('testUserCredential.loginHistories', $resolve3);
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidContext () {

        $registry = $this->getRegistry();
        $context = $this->getSaveQueryContext(new TestUser());

        (new KeyPath('credential.login'))->resolve($registry, $context);
    }

    /**
     * @expectedException \Exception
     */
    public function testFieldIsFieldNotEntity () {

        $registry = $this->getRegistry();
        $context = $this->getFindQueryContext('TestUser');

        (new KeyPath('credential.login.user'))->resolve($registry, $context);
    }

    /**
     * @expectedException \Exception
     */
    public function testFieldNotFound () {

        $registry = $this->getRegistry();
        $context = $this->getFindQueryContext('TestUser');

        (new KeyPath('credential.qweqwe'))->resolve($registry, $context);
    }

    /**
     * @expectedException \Exception
     */
    public function testStarFieldNotAtTheEnd () {

        $registry = $this->getRegistry();
        $context = $this->getFindQueryContext('TestUser');

        (new KeyPath('credential.*.date'))->resolve($registry, $context);
    }

    /**
     * @expectedException \Exception
     */
    public function testAliasNotFound () {

        $registry = $this->getRegistry('TestCredential');
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