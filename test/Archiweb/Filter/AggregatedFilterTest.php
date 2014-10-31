<?php

namespace Archiweb\Filter;

use Archiweb\TestCase;

class AggregatedFilterTest extends TestCase {

    public function testGetEntity () {

        $operator = $this->getMockLogicOperator();
        $aggregateFilter = new AggregatedFilter('project', 'myProject', $operator);
        $entity = $aggregateFilter->getEntity();
        $this->assertEquals('project', $entity);

    }

    public function testGetName () {

        $operator = $this->getMockLogicOperator();
        $aggregateFilter = new AggregatedFilter('project', 'myProject', $operator);
        $name = $aggregateFilter->getName();
        $this->assertEquals('myProject', $name);

    }

    public function testAddFilter () {

        $operator = $this->getMockLogicOperator();
        $aggregateFilter = new AggregatedFilter('project', 'myProject', $operator);
        $filter = $this->getMockFilter();
        $aggregateFilter->addFilter($filter);
        $filters = $aggregateFilter->getFilters();
        $this->assertEquals($filters[0], $filter);
    }

    public function testGetExpression () {

        $operator = $this->getMockLogicOperator();
        $aggregateFilter = new AggregatedFilter('project', 'myProject', $operator);
        $filter1 = $this->getMockFilter();
        $expression1 = $this->getMockExpression();
        $filter1->method('getExpression')->willReturn($expression1);
        $aggregateFilter->addFilter($filter1);
        $filter2 = $this->getMockFilter();
        $expression2 = $this->getMockExpression();
        $filter2->method('getExpression')->willReturn($expression2);
        $aggregateFilter->addFilter($filter2);
        $expression = $aggregateFilter->getExpression();

        $expressions = $expression->getExpressions();
        $this->assertSame($filter1->getExpression(), $expressions[0]);
        $this->assertSame($filter2->getExpression(), $expressions[1]);

        $aggregateFilter = new AggregatedFilter('project', 'myProject', $operator);
        $expression = $aggregateFilter->getExpression();
        $this->assertInstanceOf('\Archiweb\Expression\NAryExpression', $expression);

    }

}
