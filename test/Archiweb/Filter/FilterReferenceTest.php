<?php

namespace Archiweb\Filter;

class FilterReferenceTest extends \PHPUnit_Framework_TestCase
{

    private $referenceFilter;

    function __construct() {

        $this->referenceFilter = new FilterReference('project','myProject');

    }

    public function testGetEntity() {

        $entity = $this->referenceFilter->getEntity();
        $this->assertEquals('project', $entity);

    }

    public function testGetName() {

        $name = $this->referenceFilter->getName();
        $this->assertEquals('myProject', $name);

    }

}
