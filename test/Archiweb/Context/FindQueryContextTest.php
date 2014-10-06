<?php

namespace Archiweb\Context;


use Archiweb\Operation;
use Archiweb\TestCase;

class FindQueryContextTest extends TestCase {

    /**
     * @var RequestContext
     */
    protected $context;

    /**
     *
     */
    public function testEntity () {

        $entity = 'Company';
        $ctx = $this->getFindQueryContext($entity);

        $this->assertSame($entity, $ctx->getEntity());

    }

    /**
     *
     */
    public function testFilters () {

        // empty rule list
        $ctx = $this->getFindQueryContext('Company');
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
        $ctx = $this->getFindQueryContext('Company');
        $this->assertSame([], $ctx->getKeyPaths());

        // only one keyPath
        $keyPath = $this->getMockFieldKeyPath();
        $ctx->addKeyPath($keyPath);
        $this->assertSame([$keyPath], $ctx->getKeyPaths());

        // several keyPaths
        $keyPaths = [$this->getMockFieldKeyPath(), $this->getMockFieldKeyPath()];
        foreach ($keyPaths as $k) {
            $ctx->addKeyPath($k);
        }
        $keyPaths[] = $keyPath;
        $this->assertSameSize($keyPaths, $ctx->getKeyPaths());
        foreach ($keyPaths as $f) {
            $this->assertContains($f, $ctx->getKeyPaths());
        }

    }

    public function testGetApplicationContext () {

        $ctx = $this->getFindQueryContext('Company');
        $appCtx = $ctx->getApplicationContext();

        $this->assertInstanceOf('\Archiweb\Context\ApplicationContext', $appCtx);

    }

    /**
     *
     */
    public function testParams () {

        $ctx = $this->getFindQueryContext('Company');

        $array = ['a', 'b' => 2, ['c']];

        $ctx->setParams($array);

        $this->assertSame($array, $ctx->getParams());
        $this->assertSame($array[0], $ctx->getParam(0));
        $this->assertSame($array['b'], $ctx->getParam('b'));
        $this->assertSame(NULL, $ctx->getParam('qwe'));

    }

    public function testAddJoinedEntities () {

        $ctx = $this->getFindQueryContext('Company');
        $joinedEntity = 'qwe';
        $ctx->addJoinedEntity($joinedEntity);

        $this->assertInternalType('array', $ctx->getJoinedEntities());
        $this->assertCount(1, $ctx->getJoinedEntities());
        $this->assertSame($joinedEntity, $ctx->getJoinedEntities()[0]);

    }

} 