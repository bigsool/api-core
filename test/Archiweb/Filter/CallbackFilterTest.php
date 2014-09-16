<?php

namespace Archiweb\Filter;

class CallbackFilterTest extends \PHPUnit_Framework_TestCase {


    public function testGetEntity() {
        $callBackFilter = new CallbackFilter('project', 'myProject', 'select', function () {
            return 'project.owner = 1';
        });
        $entity = $callBackFilter->getEntity();
        $this->assertEquals('project', $entity);

    }

    public function testGetName() {
        $callBackFilter = new CallbackFilter('project', 'myProject', 'select', function () {
            return 'project.owner = 1';
        });
        $name = $callBackFilter->getName();
        $this->assertEquals('myProject', $name);

    }

    public function testGetExpression() {
        $callBackFilter = new CallbackFilter('project', 'myProject', 'select', function () {
            return 'project.owner = 1';
        });
        $expression = $callBackFilter->getExpression();
        $this->assertEquals('project.owner = 1', $expression);

    }

}
