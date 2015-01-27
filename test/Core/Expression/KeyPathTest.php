<?php


namespace Core\Expression;


use Core\Model\HostedProject;
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

        $registry = $this->getRegistry('HostedProject');
        $context = $this->getFindQueryContext('HostedProject');

        $param = 'creator.company.storage.url';

        $param1 = new KeyPath($param);
        $resolve1 = $param1->resolve($registry, $context);

        $this->assertEquals('hostedProjectCreatorCompanyStorage.url', $resolve1);

        $param2 = new KeyPath('name');
        $resolve2 = $param2->resolve($registry, $context);

        $this->assertEquals('hostedProject.name', $resolve2);

        $param3 = new KeyPath('creator.company.storage');
        $resolve3 = $param3->resolve($registry, $context);

        $this->assertEquals('hostedProjectCreatorCompany.storage', $resolve3);
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidContext () {

        $registry = $this->getRegistry();
        $context = $this->getSaveQueryContext(new HostedProject());

        (new KeyPath('creator.company.storage.url'))->resolve($registry, $context);
    }

    /**
     * @expectedException \Exception
     */
    public function testFieldIsFieldNotEntity () {

        $registry = $this->getRegistry();
        $context = $this->getFindQueryContext('HostedProject');

        (new KeyPath('creator.name.company.storage.url'))->resolve($registry, $context);
    }

    /**
     * @expectedException \Exception
     */
    public function testFieldNotFound () {

        $registry = $this->getRegistry();
        $context = $this->getFindQueryContext('HostedProject');

        (new KeyPath('creator.qweqwe'))->resolve($registry, $context);
    }

    /**
     * @expectedException \Exception
     */
    public function testStarFieldNotAtTheEnd () {

        $registry = $this->getRegistry();
        $context = $this->getFindQueryContext('HostedProject');

        (new KeyPath('creator.*.name'))->resolve($registry, $context);
    }

    /**
     * @expectedException \Exception
     */
    public function testAliasNotFound () {

        $registry = $this->getRegistry('HostedProject');
        $context = $this->getFindQueryContext('Product');

        $param = '*';

        (new KeyPath($param))->resolve($registry, $context);

    }

    /**
     * @expectedException \Exception
     */
    public function testMoreThanOneAliasFound () {

        $registry = $this->getRegistry('HostedProject');
        $context = $this->getFindQueryContext('HostedProject');

        (new KeyPath('creator'))->resolve($registry, $context);
        (new KeyPath('sharedHostedProjects.participant'))->resolve($registry, $context);

        $keyPath = new KeyPath('name');
        $keyPath->setRootEntity('User');
        $keyPath->resolve($registry, $context);

    }

} 