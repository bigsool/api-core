<?php

namespace Core\Filter;


class StringFilterTest extends \PHPUnit_Framework_TestCase {

    public function testGetEntity () {

        $strFilter = new StringFilter('project', 'myProject', 'project.owner = 1');
        $entity = $strFilter->getEntity();
        $this->assertEquals('project', $entity);

    }

    public function testGetName () {

        $strFilter = new StringFilter('project', 'myProject', 'project.owner = 1');
        $name = $strFilter->getName();
        $this->assertEquals('myProject', $name);

    }

    public function testGetExpression () {

        $strFilter = new StringFilter('project', 'myProject', 'project.owner = 1');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Core\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Core\Operator\EqualOperator', $operator);
        $leftExpression = $expression->getLeft();
        $this->assertInstanceOf('\Core\Expression\KeyPath', $leftExpression);
        $this->assertSame('project.owner', $leftExpression->getValue());
        $rightExpression = $expression->getRight();
        $this->assertInstanceOf('\Core\Expression\Value', $rightExpression);
        $this->assertSame('Core\Expression\Value', get_class($rightExpression));
        $this->assertEquals('1', $rightExpression->getValue());

        $strFilter = new StringFilter('project', 'myProject', ':number != project.owner');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Core\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Core\Operator\NotEqualOperator', $operator);
        $leftExpression = $expression->getLeft();
        $this->assertInstanceOf('\Core\Expression\Parameter', $leftExpression);
        $this->assertSame(':number', $leftExpression->getValue());
        $rightExpression = $expression->getRight();
        $this->assertInstanceOf('\Core\Expression\KeyPath', $rightExpression);
        $this->assertSame('project.owner', $rightExpression->getValue());

        $strFilter = new StringFilter('project', 'myProject', '1 >= :number');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Core\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Core\Operator\GreaterOrEqualOperator', $operator);
        $leftExpression = $expression->getLeft();
        $this->assertInstanceOf('\Core\Expression\Value', $leftExpression);
        $this->assertSame('Core\Expression\Value', get_class($leftExpression));
        $this->assertEquals('1', $leftExpression->getValue());
        $rightExpression = $expression->getRight();
        $this->assertInstanceOf('\Core\Expression\Parameter', $rightExpression);
        $this->assertSame(':number', $rightExpression->getValue());

        $strFilter = new StringFilter('project', 'myProject', 'project.owner <= "qwe"');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Core\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Core\Operator\LowerOrEqualOperator', $operator);
        $leftExpression = $expression->getLeft();
        $this->assertInstanceOf('\Core\Expression\KeyPath', $leftExpression);
        $this->assertSame('project.owner', $leftExpression->getValue());
        $rightExpression = $expression->getRight();
        $this->assertInstanceOf('\Core\Expression\Value', $rightExpression);
        $this->assertSame('Core\Expression\Value', get_class($rightExpression));
        $this->assertEquals('qwe', $rightExpression->getValue());

        $strFilter = new StringFilter('project', 'myProject', 'project.owner > 1');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Core\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Core\Operator\GreaterThanOperator', $operator);
        $leftExpression = $expression->getLeft();
        $this->assertInstanceOf('\Core\Expression\KeyPath', $leftExpression);
        $this->assertSame('project.owner', $leftExpression->getValue());
        $rightExpression = $expression->getRight();
        $this->assertInstanceOf('\Core\Expression\Value', $rightExpression);
        $this->assertSame('Core\Expression\Value', get_class($rightExpression));
        $this->assertEquals('1', $rightExpression->getValue());

        $strFilter = new StringFilter('project', 'myProject', 'project.owner < 1');
        $expression = $strFilter->getExpression();
        $this->assertInstanceOf('\Core\Expression\BinaryExpression', $expression);
        $operator = $expression->getOperator();
        $this->assertInstanceOf('\Core\Operator\LowerThanOperator', $operator);
        $leftExpression = $expression->getLeft();
        $this->assertInstanceOf('\Core\Expression\KeyPath', $leftExpression);
        $this->assertSame('project.owner', $leftExpression->getValue());
        $rightExpression = $expression->getRight();
        $this->assertInstanceOf('\Core\Expression\Value', $rightExpression);
        $this->assertSame('Core\Expression\Value', get_class($rightExpression));
        $this->assertEquals('1', $rightExpression->getValue());

    }

}
