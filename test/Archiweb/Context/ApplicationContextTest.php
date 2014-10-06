<?php


namespace Archiweb\Context;


use Archiweb\TestCase;

class ApplicationContextTest extends TestCase {

    public function testRuleManager () {

        $ctx = new ApplicationContext();
        $ruleMgr = $this->getMockRuleProcessor();

        $ctx->setRuleManager($ruleMgr);
        $this->assertSame($ruleMgr, $ctx->getRuleManager());

    }

    public function testFilters () {

        $ctx = new ApplicationContext();
        $filters = [$this->getMockFilter(), $this->getMockFilter(), $this->getMockFilter()];

        $this->assertSame([], $ctx->getFilters());

        $ctx->addFilter($filters[0]);
        $this->assertSame([$filters[0]], $ctx->getFilters());

        $ctx->addFilter($filters[1]);
        $ctx->addFilter($filters[2]);
        $this->assertSame($filters, $ctx->getFilters());

    }

    public function testFields () {

        $ctx = new ApplicationContext();
        $fields = [$this->getMockField(), $this->getMockField(), $this->getMockField()];

        $this->assertSame([], $ctx->getFields());

        $ctx->addField($fields[0]);
        $this->assertSame([$fields[0]], $ctx->getFields());

        $ctx->addField($fields[1]);
        $ctx->addField($fields[2]);
        $this->assertSame($fields, $ctx->getFields());

    }

    public function testRules () {

        $ctx = new ApplicationContext();
        $rules = [$this->getMockRule(), $this->getMockRule(), $this->getMockRule()];

        $this->assertSame([], $ctx->getRules());

        $ctx->addRule($rules[0]);
        $this->assertSame([$rules[0]], $ctx->getRules());

        $ctx->addRule($rules[1]);
        $ctx->addRule($rules[2]);
        $this->assertSame($rules, $ctx->getRules());

    }

    public function testGetNewRegistry () {

        $registry = $this->getApplicationContext()->getNewRegistry();

        $this->assertInstanceOf('\Archiweb\Registry', $registry);

    }

    public function testGetClassMetadata () {

        $classMetadata = $this->getApplicationContext()->getClassMetadata('\Archiweb\Model\Company');

        $this->assertInstanceOf('\Doctrine\ORM\Mapping\ClassMetadata', $classMetadata);
        $this->assertSame('Archiweb\Model\Company', $classMetadata->getName());

    }

    public function testGetFieldsByEntity () {

        $ctx = $this->getApplicationContext();

        $this->assertEmpty($ctx->getFieldsByEntity('Company'));

        $fields[] = $field = $this->getMockField();
        $field->method('getEntity')->willReturn('Company');
        $ctx->addField($field);
        $this->assertSame($fields, $ctx->getFieldsByEntity('Company'));

        $field = $this->getMockField();
        $field->method('getEntity')->willReturn('Product');
        $ctx->addField($field);
        $this->assertSame($fields, $ctx->getFieldsByEntity('Company'));
        $this->assertSame([$field], $ctx->getFieldsByEntity('Product'));

    }

    public function testGetFieldByEntityAndName () {

        $ctx = $this->getApplicationContext();

        $fields[] = $field = $this->getMockField();
        $field->method('getEntity')->willReturn('Company');
        $field->method('getName')->willReturn('name');
        $ctx->addField($field);
        $this->assertSame($field, $ctx->getFieldByEntityAndName('Company', 'name'));

    }

    public function testGetFilterByEntityAndName () {

        $ctx = $this->getApplicationContext();

        $filters[] = $filter = $this->getMockFilter();
        $filter->method('getEntity')->willReturn('Company');
        $filter->method('getName')->willReturn('name');
        $ctx->addFilter($filter);
        $this->assertSame($filter, $ctx->getFilterByEntityAndName('Company', 'name'));

    }

    /**
     * @expectedException \Exception
     */
    public function testGetFieldByEntityAndNameNotFound () {

        $this->getApplicationContext()->getFieldByEntityAndName('Company', 'name');

    }

    /**
     * @expectedException \Exception
     */
    public function testGetFilterByEntityAndNameNotFound () {

        $this->getApplicationContext()->getFilterByEntityAndName('Company', 'name');

    }

} 