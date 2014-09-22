<?php

namespace Archiweb\Context;


use Archiweb\TestCase;
use Doctrine\ORM\EntityManager;

class QueryContextTest extends TestCase {

    /**
     * @var RequestContext
     */
    protected $context;

    /**
     *
     */
    public function testParams () {

        $array = [$this->getParameterMock('a'), 'b' => $this->getParameterMock(2), $this->getParameterMock(['c'])];

        $actionCtx = new QueryContext($this->getApplicationContext());
        $actionCtx->setParams($array);

        $this->assertSame($array, $actionCtx->getParams());
        $this->assertSame($array[0], $actionCtx->getParam(0));
        $this->assertSame($array['b'], $actionCtx->getParam('b'));

    }

    /**
     * @param $value
     *
     * @return Parameter
     */
    protected function getParameterMock ($value) {

        $mock = $this->getMockBuilder('\Archiweb\Parameter\Parameter')
                     ->disableOriginalConstructor()
                     ->getMock();
        $mock->method('getValue')->willReturn($value);

        return $mock;

    }

    /**
     *
     */
    public function testFilters () {

        // empty rule list
        $ctx = new QueryContext($this->getApplicationContext());
        $this->assertSame([], $ctx->getFilters());

        // only one rule
        $filter = $this->getFilterMock();
        $ctx->addFilter($filter);
        $this->assertSame([$filter], $ctx->getFilters());

        // several rules
        $filters = [$this->getFilterMock(), $this->getFilterMock()];
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
     * @return Filter
     */
    protected function getFilterMock () {

        return $this->getMockBuilder('\Archiweb\Filter\Filter')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     *
     */
    public function testFields () {

        // empty rule list
        $ctx = new QueryContext($this->getApplicationContext());
        $this->assertSame([], $ctx->getFields());

        // only one rule
        $field = $this->getFieldMock();
        $ctx->addField($field);
        $this->assertSame([$field], $ctx->getFields());

        // several rules
        $fields = [$this->getFieldMock(), $this->getFieldMock()];
        foreach ($fields as $f) {
            $ctx->addField($f);
        }
        $fields[] = $field;
        $this->assertSameSize($fields, $ctx->getFields());
        foreach ($fields as $f) {
            $this->assertContains($f, $ctx->getFields());
        }

    }

    /**
     * @return Field
     */
    protected function getFieldMock () {

        return $this->getMockBuilder('\Archiweb\Field')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

} 