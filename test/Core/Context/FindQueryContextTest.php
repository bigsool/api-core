<?php

namespace Core\Context;


use Core\Auth;
use Core\TestCase;

class FindQueryContextTest extends TestCase {

    /**
     * @var RequestContext
     */
    protected $context;

    /**
     * @expectedException \Exception
     */
    public function testInvalidTypeConstructor () {

        new FindQueryContext(new \stdClass());

    }

    /**
     *
     */
    public function testEntity () {

        $entity = 'TestCompany';
        $ctx = $this->getFindQueryContext($entity);

        $this->assertSame($entity, $ctx->getEntity());

    }

    /**
     *
     */
    public function testFilters () {

        // empty rule list
        $ctx = $this->getFindQueryContext('TestCompany');
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
    public function testKeyPaths () {

        // empty keyPath list
        $ctx = $this->getFindQueryContext('TestCompany');
        $this->assertSame([], $ctx->getFields());

        // only one keyPath
        $keyPath = $this->getMockRelativeField();
        $ctx->addField($keyPath);
        $this->assertSame([$keyPath], $ctx->getFields());

        // several keyPaths
        $keyPaths = [$this->getMockRelativeField(), $this->getMockRelativeField()];
        foreach ($keyPaths as $k) {
            $ctx->addField($k);
        }
        $keyPaths[] = $keyPath;
        $this->assertSameSize($keyPaths, $ctx->getFields());
        foreach ($keyPaths as $f) {
            $this->assertContains($f, $ctx->getFields());
        }

        // keyPath with alias
        $keyPath = $this->getMockRelativeField();
        $setAliasCalled = false;
        $alias = 'qwe';
        $keyPath->method('setAlias')->will($this->returnCallback(function ($alias2) use (&$setAliasCalled, &$alias) {

            $setAliasCalled = $alias === $alias2;

        }));
        $ctx->addField($keyPath, $alias);
        $this->assertTrue($setAliasCalled);

    }

    /**
     *
     */
    public function testParams () {

        $ctx = $this->getFindQueryContext('TestCompany');

        $array = ['a', 'b' => 2, ['c']];

        $ctx->setParams($array);

        $this->assertSame($array, $ctx->getParams());
        $this->assertSame($array[0], $ctx->getParam(0));
        $this->assertSame($array['b'], $ctx->getParam('b'));
        $this->assertSame(NULL, $ctx->getParam('qwe'));

    }

    public function testAddJoinedEntities () {

        $ctx = $this->getFindQueryContext('TestCompany');
        $joinedEntity = 'qwe';
        $ctx->addJoinedEntity($joinedEntity);

        $this->assertInternalType('array', $ctx->getJoinedEntities());
        $this->assertCount(1, $ctx->getJoinedEntities());
        $this->assertSame($joinedEntity, $ctx->getJoinedEntities()[0]);

    }

} 