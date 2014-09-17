<?php

namespace Archiweb\Filter;

class FilterReferenceTest extends \PHPUnit_Framework_TestCase {

    public function testGetEntity () {

        $referenceFilter = new FilterReference('project', 'myProject');
        $entity = $referenceFilter->getEntity();
        $this->assertEquals('project', $entity);

    }

    public function testGetName () {

        $referenceFilter = new FilterReference('project', 'myProject');
        $name = $referenceFilter->getName();
        $this->assertEquals('myProject', $name);

    }

}
