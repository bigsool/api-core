<?php


namespace Archiweb\Field;


use Archiweb\TestCase;

class KeyPathTest extends TestCase {

    /**
     *
     */
    public function testResolveEntity () {

        $registry = $this->getRegistry('HostedProject');
        $context = $this->getFindQueryContext('HostedProject');

        $param = 'creator.company.storage';

        $param1 = new KeyPath($param);
        $resolve1 = $param1->resolve($registry, $context);

        $this->assertEquals('hostedProjectCreatorCompanyStorage', $resolve1);
    }

    public function testAlias() {

        $registry = $this->getRegistry('HostedProject');
        $context = $this->getFindQueryContext('HostedProject');

        $alias = 'qwe';

        $param = new KeyPath('creator.company.storage');
        $param->setAlias($alias);
        $resolve = $param->resolve($registry, $context);

        $this->assertEquals($alias, $resolve);

    }

} 