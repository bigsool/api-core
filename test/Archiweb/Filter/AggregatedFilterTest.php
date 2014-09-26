<?php

namespace Archiweb\Filter;


class AggregatedFilterTest extends \PHPUnit_Framework_TestCase {

    public function testGetEntity () {

        $operator = $this->getMock('\Archiweb\Operator\LogicOperator');
        $aggregateFilter = new AggregatedFilter('project', 'myProject', 'select', $operator);
        $entity = $aggregateFilter->getEntity();
        $this->assertEquals('project', $entity);

    }

    public function testGetName () {

        $operator = $this->getMock('\Archiweb\Operator\LogicOperator');
        $aggregateFilter = new AggregatedFilter('project', 'myProject', 'select', $operator);
        $name = $aggregateFilter->getName();
        $this->assertEquals('myProject', $name);

    }

    public function testAddFilter () {

        $operator = $this->getMock('\Archiweb\Operator\LogicOperator');
        $aggregateFilter = new AggregatedFilter('project', 'myProject', 'select', $operator);
        $strFilter = new FilterReference('project', 'myProject');
        $aggregateFilter->addFilter($strFilter);
        $filters = $aggregateFilter->getFilters();
        $this->assertEquals($filters[0], $strFilter);
    }

    public function testGetExpression () {

        $operator = $this->getMock('\Archiweb\Operator\LogicOperator');
        $aggregateFilter = new AggregatedFilter('project', 'myProject', 'select', $operator);
        $strFilter1 = new StringFilter('project', 'myProject', 'project.owner = 1', 'select');
        $aggregateFilter->addFilter($strFilter1);
        $strFilter2 = new StringFilter('project', 'myProject', 'project.id = 2', 'select');
        $aggregateFilter->addFilter($strFilter2);
        $expression = $aggregateFilter->getExpression();

        $expressions = $expression->getExpressions();
        $this->assertSame($strFilter1->getExpression(), $expressions[0]);
        $this->assertSame($strFilter2->getExpression(), $expressions[1]);

    }

    public function testGetOperator () {

        $operator = $this->getMock('\Archiweb\Operator\LogicOperator');
        $aggregateFilter = new AggregatedFilter('project', 'myProject', 'select', $operator);
        $strFilter1 = new StringFilter('project', 'myProject', 'project.owner = 1', 'select');
        $aggregateFilter->addFilter($strFilter1);
        $expression = $aggregateFilter->getExpression();
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Archiweb\Operator\LogicOperator', $operator);

    }

}
