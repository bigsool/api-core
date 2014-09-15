<?php

namespace Archiweb\Filter;


class StringFilterTest extends \PHPUnit_Framework_TestCase
{

    private $strFilter;

    function __construct() {
        $this->strFilter = new StringFilter('project', 'myProject', 'project.owner = 1', 'select');
    }

    public function testGetEntity() {

        $entity = $this->strFilter->getEntity();
        $this->assertEquals('project', $entity);

    }

    public function testGetName() {

        $name = $this->strFilter->getName();
        $this->assertEquals('myProject', $name);

    }

    public function testGetExpression() {

        $expression = $this->strFilter->getExpression();
        $this->assertEquals('project.owner = 1', $expression);

    }

}
