<?php

namespace Archiweb\Filter;

class CallbackFilterTest extends \PHPUnit_Framework_TestCase {

    private $callBackFilter;

    function __construct() {

        $this->callBackFilter = new CallbackFilter('project', 'myProject', 'select', function () {
          return 'project.owner = 1';
        });

    }

    public function testGetEntity() {

        $entity = $this->callBackFilter->getEntity();
        $this->assertEquals('project', $entity);

    }

    public function testGetName() {

        $name = $this->callBackFilter->getName();
        $this->assertEquals('myProject', $name);

    }

    public function testGetExpression() {

        $expression = $this->callBackFilter->getExpression();
        $this->assertEquals('project.owner = 1', $expression);

    }

}
