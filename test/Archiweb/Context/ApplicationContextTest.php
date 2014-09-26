<?php


namespace Archiweb\Context;


use Archiweb\TestCase;

class ApplicationContextTest extends TestCase {

    public function testRuleManager () {

        $ctx = new ApplicationContext();
        $ruleMgr = $this->getMockRuleManager();

        $ctx->setRuleManager($ruleMgr);
        $this->assertSame($ruleMgr, $ctx->getRuleManager());

    }

    public function testEntityManager () {

        $ctx = new ApplicationContext();
        $em = $this->getMockEntityManager();

        $getEM = new \ReflectionMethod($ctx, 'getEntityManager');
        $getEM->setAccessible(true);

        $ctx->setEntityManager($em);

        $called = false;
        $receiver = $this->getMockEntityManagerReceiver();
        $receiver->method('setEntityManager')->will($this->returnCallback(function ($entityManager) use (
            $em, &$called
        ) {

            $this->assertSame($em, $entityManager);
            $called = true;

        }));

        $ctx->getEntityManager($receiver);

    }

    public function testFilters() {

        $ctx = new ApplicationContext();
        $filters = [$this->getMockFilter(), $this->getMockFilter(), $this->getMockFilter()];

        $this->assertSame([], $ctx->getFilters());

        $ctx->addFilter($filters[0]);
        $this->assertSame([$filters[0]], $ctx->getFilters());

        $ctx->addFilter($filters[1]);
        $ctx->addFilter($filters[2]);
        $this->assertSame($filters, $ctx->getFilters());

    }

    public function testFields() {

        $ctx = new ApplicationContext();
        $fields = [$this->getMockField(), $this->getMockField(), $this->getMockField()];

        $this->assertSame([], $ctx->getFields());

        $ctx->addField($fields[0]);
        $this->assertSame([$fields[0]], $ctx->getFields());

        $ctx->addField($fields[1]);
        $ctx->addField($fields[2]);
        $this->assertSame($fields, $ctx->getFields());

    }

    public function testRules() {

        $ctx = new ApplicationContext();
        $rules = [$this->getMockRule(), $this->getMockRule(), $this->getMockRule()];

        $this->assertSame([], $ctx->getRules());

        $ctx->addRule($rules[0]);
        $this->assertSame([$rules[0]], $ctx->getRules());

        $ctx->addRule($rules[1]);
        $ctx->addRule($rules[2]);
        $this->assertSame($rules, $ctx->getRules());

    }

} 