<?php


namespace Core\Field;


use Core\TestCase;

class KeyPathTest extends TestCase {

    /**
     *
     */
    public function testResolveEntity () {

        $registry = $this->getRegistry('TestCompany');
        $context = $this->getFindQueryContext('TestCompany');

        $param = 'owner.company.storage';

        $param1 = new KeyPath($param);
        $resolve1 = $param1->resolve($registry, $context);

        $param2 = new KeyPath('*');

        $this->assertEquals('testCompanyOwnerCompanyStorage', $resolve1);

        $this->assertInstanceOf('\Core\Field\StarField', $param2->getField($context));

    }

    public function testAlias () {

        $alias = 'qwe';

        $param = new KeyPath('owner.company.storage');
        $param->setAlias($alias);

        $this->assertEquals($alias, $param->getAlias());

    }

} 