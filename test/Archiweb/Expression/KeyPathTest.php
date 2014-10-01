<?php


namespace Archiweb\Expression;


use Archiweb\Model\HostedProject;
use Archiweb\TestCase;

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

        $registry = $this->getRegistry();
        $context = $this->getFindQueryContext('HostedProject');

        $param = 'creator.company.storage.url';

        $param1 = new KeyPath($param);
        $resolve1 = $param1->resolve($registry, $context);

        $this->assertEquals('hostedProjectCreatorCompanyStorage.url', $resolve1);

        $param1 = new KeyPath('name');
        $resolve1 = $param1->resolve($registry, $context);

        $this->assertEquals('hostedProject.name', $resolve1);
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
     *
     */
    public function testResolveEntity () {

        $registry = $this->getRegistry();
        $context = $this->getFindQueryContext('HostedProject');

        $param = 'creator.company.storage';

        $param1 = new KeyPath($param);
        $resolve1 = $param1->resolve($registry, $context);

        $this->assertEquals('hostedProjectCreatorCompanyStorage', $resolve1);
    }

} 