<?php

namespace Archiweb\Context;


use Archiweb\Operation;
use Archiweb\TestCase;

class QueryContextTest extends TestCase {

    /**
     * @var RequestContext
     */
    protected $context;

    /**
     *
     */
    public function testParams () {

        $array = ['a', 'b' => 2, ['c']];

        $ctx = $this->getQueryContext();
        $ctx->setParams($array);

        $this->assertSame($array, $ctx->getParams());
        $this->assertSame($array[0], $ctx->getParam(0));
        $this->assertSame($array['b'], $ctx->getParam('b'));

    }

    /**
     *
     */
    public function testEntity () {

        $entity = 'Company';
        $ctx = $this->getQueryContext();
        $ctx->setEntity($entity);

        $this->assertSame($entity, $ctx->getEntity());

    }

    /**
     *
     */
    public function testCommand () {

        $ctx = $this->getQueryContext();
        $operation = 'SELECT';
        $ctx->setCommand('SELECT');

        $this->assertSame($operation, $ctx->getCommand());

    }

    /**
     *
     */
    public function testFilters () {

        // empty rule list
        $ctx = $this->getQueryContext();
        $this->assertSame([], $ctx->getFilters());

        // only one rule
        $filter = $this->getMockFilter();
        $ctx->addFilter($filter);
        $this->assertSame([$filter], $ctx->getFilters());

        // several rules
        $filters = [$this->getMockFilter(), $this->getMockFilter()];
        foreach ($filters as $f) {
            $ctx->addFilter($f);
        }
        $filters[] = $filter;
        $this->assertSameSize($filters, $ctx->getFilters());
        foreach ($filters as $f) {
            $this->assertContains($f, $ctx->getFilters());
        }

    }

    /**
     *
     */
    public function testFields () {

        // empty rule list
        $ctx = $this->getQueryContext();
        $this->assertSame([], $ctx->getFields());

        // only one rule
        $field = $this->getMockField();
        $ctx->addField($field);
        $this->assertSame([$field], $ctx->getFields());

        // several rules
        $fields = [$this->getMockField(), $this->getMockField()];
        foreach ($fields as $f) {
            $ctx->addField($f);
        }
        $fields[] = $field;
        $this->assertSameSize($fields, $ctx->getFields());
        foreach ($fields as $f) {
            $this->assertContains($f, $ctx->getFields());
        }

    }

    public function testGetApplicationContext () {

        $ctx = $this->getQueryContext();
        $appCtx = $ctx->getApplicationContext();

        $this->assertInstanceOf('\Archiweb\Context\ApplicationContext', $appCtx);

    }

} 