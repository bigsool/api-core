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

    /**
     *
     */
    public function testResolveUsedTwice () {

        $registry = $this->getRegistry('TestCompany');
        $context = $this->getFindQueryContext('TestCompany');

        $param = 'owner.company.storage';

        $param1 = new KeyPath($param);
        $resolve1 = $param1->resolve($registry, $context);

        $joins = $registry->getJoins();
        $this->assertCount(3, $joins);

        $this->assertEquals('testCompanyOwnerCompanyStorage', $resolve1);

        $resolve1 = $param1->resolve($registry, $context);

        $joins = $registry->getJoins();
        $this->assertCount(3, $joins);

        $this->assertEquals('testCompanyOwnerCompanyStorage', $resolve1);

        $registry2 = $this->getRegistry('TestCompany');
        $context2 = $this->getFindQueryContext('TestCompany');

        $resolve2 = $param1->resolve($registry2, $context2);
        $this->assertEquals('testCompanyOwnerCompanyStorage', $resolve2);

        $joins = $registry2->getJoins();
        $this->assertCount(3, $joins);

    }

    public function testAlias () {

        $alias = 'qwe';

        $param = new KeyPath('owner.company.storage');
        $param->setAlias($alias);

        $this->assertEquals($alias, $param->getAlias());

    }

} 