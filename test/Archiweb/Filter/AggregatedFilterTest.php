<?php

namespace Archiweb\Filter;


class AggregatedFilterTest extends \PHPUnit_Framework_TestCase {

    private $aggregateFilter;

    function __construct() {

        $operator = $this->getMock('\Archiweb\Operator\LogicOperator');
        $this->aggregateFilter = new AggregatedFilter('project','myProject','select', $operator);

    }

    public function testGetEntity() {

        $entity = $this->aggregateFilter->getEntity();
        $this->assertEquals('project', $entity);

    }

    public function testGetName() {

        $name = $this->aggregateFilter->getName();
        $this->assertEquals('myProject', $name);

    }

    public function testAddFilter() {

        $strFilter = new FilterReference('project','myProject');
        $this->aggregateFilter->addFilter($strFilter);
        $filters = $this->aggregateFilter->getFilters();
        $this->assertEquals($filters[0],$strFilter);

    }

}
