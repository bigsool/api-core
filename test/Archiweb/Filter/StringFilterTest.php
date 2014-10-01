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
        $leftExpression = $expression->getLeft();
        $this->assertInstanceOf('\Archiweb\Expression\KeyPath', $leftExpression);
        $this->assertSame('project.owner', $leftExpression->getValue());
        $rightExpression = $expression->getRight();
        $this->assertInstanceOf('\Archiweb\Expression\Value', $rightExpression);
        $this->assertEquals('1', $rightExpression->getValue());

        $strFilter = new StringFilter('project', 'myProject', ':number != project.owner', 'select');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Archiweb\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Archiweb\Operator\NotEqualOperator', $operator);
        $leftExpression = $expression->getLeft();
        $this->assertInstanceOf('\Archiweb\Expression\Parameter', $leftExpression);
        $this->assertSame(':number', $leftExpression->getValue());
        $rightExpression = $expression->getRight();
        $this->assertInstanceOf('\Archiweb\Expression\KeyPath', $rightExpression);
        $this->assertSame('project.owner', $rightExpression->getValue());

        $strFilter = new StringFilter('project', 'myProject', '1 >= :number', 'select');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Archiweb\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Archiweb\Operator\GreaterOrEqualOperator', $operator);
        $leftExpression = $expression->getLeft();
        $this->assertInstanceOf('\Archiweb\Expression\Value', $leftExpression);
        $this->assertEquals('1', $leftExpression->getValue());
        $rightExpression = $expression->getRight();
        $this->assertInstanceOf('\Archiweb\Expression\Parameter', $rightExpression);
        $this->assertSame(':number', $rightExpression->getValue());

        $strFilter = new StringFilter('project', 'myProject', 'project.owner <= "qwe"', 'select');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Archiweb\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Archiweb\Operator\LowerOrEqualOperator', $operator);
        $leftExpression = $expression->getLeft();
        $this->assertInstanceOf('\Archiweb\Expression\KeyPath', $leftExpression);
        $this->assertSame('project.owner', $leftExpression->getValue());
        $rightExpression = $expression->getRight();
        $this->assertInstanceOf('\Archiweb\Expression\Value', $rightExpression);
        $this->assertEquals('qwe', $rightExpression->getValue());

        $strFilter = new StringFilter('project', 'myProject', 'project.owner > 1', 'select');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Archiweb\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Archiweb\Operator\GreaterThanOperator', $operator);
        $leftExpression = $expression->getLeft();
        $this->assertInstanceOf('\Archiweb\Expression\KeyPath', $leftExpression);
        $this->assertSame('project.owner', $leftExpression->getValue());
        $rightExpression = $expression->getRight();
        $this->assertInstanceOf('\Archiweb\Expression\Value', $rightExpression);
        $this->assertEquals('1', $rightExpression->getValue());

        $strFilter = new StringFilter('project', 'myProject', 'project.owner < 1', 'select');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Archiweb\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Archiweb\Operator\LowerThanOperator', $operator);
        $leftExpression = $expression->getLeft();
        $this->assertInstanceOf('\Archiweb\Expression\KeyPath', $leftExpression);
        $this->assertSame('project.owner', $leftExpression->getValue());
        $rightExpression = $expression->getRight();
        $this->assertInstanceOf('\Archiweb\Expression\Value', $rightExpression);
        $this->assertEquals('1', $rightExpression->getValue());

    }

}
