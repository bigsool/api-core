<?php


namespace Core\Field;


use Core\TestCase;

class RelativeFieldTest extends TestCase {

    /**
     *
     */
    public function testResolveEntity () {

        $registry = $this->getRegistry('TestCompany');
        $context = $this->getFindQueryContext('TestCompany');

        $param = 'owner.company.storage';

        $param1 = new RelativeField($param);
        $resolve1 = $param1->resolve($registry, $context);

        $param2 = new RelativeField('*');

        $this->assertInternalType('array', $resolve1);
        $this->assertContainsOnlyInstancesOf('\Core\Field\ResolvableField', $resolve1);
        $this->assertCount(3, $resolve1);

        $this->assertEquals($param, $resolve1[2]->getValue());

        $this->assertInstanceOf('\Core\Field\StarField', $param2->getField($context));

    }

    /**
     *
     */
    public function testResolveUsedTwice () {

        $registry = $this->getRegistry('TestCompany');
        $context = $this->getFindQueryContext('TestCompany');

        $param = 'owner.company.storage';

        $param1 = new RelativeField($param);
        $resolve1 = $param1->resolve($registry, $context);

        $this->assertInternalType('array', $resolve1);
        $this->assertContainsOnlyInstancesOf('\Core\Field\ResolvableField', $resolve1);
        $this->assertCount(3, $resolve1);

        $this->assertEquals($param, $resolve1[2]->getValue());

        $resolve1 = $param1->resolve($registry, $context);

        $this->assertInternalType('array', $resolve1);
        $this->assertContainsOnlyInstancesOf('\Core\Field\ResolvableField', $resolve1);
        $this->assertCount(3, $resolve1);

        $this->assertEquals($param, $resolve1[2]->getValue());

        $registry2 = $this->getRegistry('TestCompany');
        $context2 = $this->getFindQueryContext('TestCompany');

        $resolve2 = $param1->resolve($registry2, $context2);

        $this->assertInternalType('array', $resolve2);
        $this->assertContainsOnlyInstancesOf('\Core\Field\ResolvableField', $resolve2);
        $this->assertCount(3, $resolve2);

    }

    public function testAlias () {

        $alias = 'qwe';

        $param = new RelativeField('owner.company.storage');
        $param->setAlias($alias);

        $this->assertEquals($alias, $param->getAlias());

    }

} 