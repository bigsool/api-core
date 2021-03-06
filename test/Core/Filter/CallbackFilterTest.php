<?php

namespace Core\Filter;

use Core\TestCase;

class CallbackFilterTest extends TestCase {

    public function testGetEntity () {

        $expression = $this->getMockExpression();

        $callBackFilter = new CallbackFilter('project', 'myProject', function () use ($expression) {

            return $expression;

        });

        $entity = $callBackFilter->getEntity();

        $this->assertEquals('project', $entity);

    }

    public function testGetName () {

        $expression = $this->getMockExpression();

        $callBackFilter = new CallbackFilter('project', 'myProject', function () use ($expression) {

            return $expression;

        });

        $name = $callBackFilter->getName();

        $this->assertEquals('myProject', $name);

    }

    public function testGetExpression () {

        $expression = $this->getMockExpression();

        $callBackFilter = new CallbackFilter('project', 'myProject', function () use ($expression) {

            return $expression;

        });

        $expressionReceived = $callBackFilter->getExpression();

        $this->assertEquals($expression, $expressionReceived);

    }

}
