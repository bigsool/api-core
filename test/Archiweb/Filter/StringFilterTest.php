<?php

namespace Archiweb\Filter;


class StringFilterTest extends \PHPUnit_Framework_TestCase {

    public function testGetEntity () {

        $strFilter = new StringFilter('project', 'myProject', 'project.owner = 1', 'select');
        $entity = $strFilter->getEntity();
        $this->assertEquals('project', $entity);

    }

    public function testGetName () {

        $strFilter = new StringFilter('project', 'myProject', 'project.owner = 1', 'select');
        $name = $strFilter->getName();
        $this->assertEquals('myProject', $name);

    }

    public function testGetExpression () {

        $strFilter = new StringFilter('project', 'myProject', 'project.owner = 1', 'select');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Archiweb\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Archiweb\Operator\EqualOperator', $operator);

        $strFilter = new StringFilter('project', 'myProject', 'project.owner != 1', 'select');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Archiweb\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Archiweb\Operator\NotEqualOperator', $operator);

        $strFilter = new StringFilter('project', 'myProject', 'project.owner >= 1', 'select');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Archiweb\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Archiweb\Operator\GreaterOrEqualOperator', $operator);

        $strFilter = new StringFilter('project', 'myProject', 'project.owner <= 1', 'select');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Archiweb\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Archiweb\Operator\LowerOrEqualOperator', $operator);

        $strFilter = new StringFilter('project', 'myProject', 'project.owner > 1', 'select');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Archiweb\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Archiweb\Operator\GreaterThanOperator', $operator);

        $strFilter = new StringFilter('project', 'myProject', 'project.owner < 1', 'select');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Archiweb\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Archiweb\Operator\LowerThanOperator', $operator);

    }

}
