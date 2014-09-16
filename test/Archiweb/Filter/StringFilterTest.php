<?php

namespace Archiweb\Filter;


class StringFilterTest extends \PHPUnit_Framework_TestCase {

    public function testGetEntity() {
        $strFilter = new StringFilter('project', 'myProject', 'project.owner = 1', 'select');
        $entity = $strFilter->getEntity();
        $this->assertEquals('project', $entity);

    }

    public function testGetName() {
        $strFilter = new StringFilter('project', 'myProject', 'project.owner = 1', 'select');
        $name = $strFilter->getName();
        $this->assertEquals('myProject', $name);

    }

    public function testGetExpression() {
        $strFilter = new StringFilter('project', 'myProject', 'project.owner = 1', 'select');
        $expression = $strFilter->getExpression();
        $this->assertEquals('project.owner = 1', $expression);

    }

}