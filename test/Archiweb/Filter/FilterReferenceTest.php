<?php

namespace Archiweb\Filter;

use Archiweb\TestCase;

class FilterReferenceTest extends TestCase {

    public function testGetEntity () {

        $appCtx = $this->getMockApplicationContext();
        $referenceFilter = new FilterReference($appCtx, 'project', 'myProject');
        $entity = $referenceFilter->getEntity();
        $this->assertEquals('project', $entity);

    }

    public function testGetName () {

        $appCtx = $this->getMockApplicationContext();
        $referenceFilter = new FilterReference($appCtx, 'project', 'myProject');
        $name = $referenceFilter->getName();
        $this->assertEquals('myProject', $name);

    }

    public function testGetExpression () {

        $appCtx = $this->getMockApplicationContext();
        $filter = $this->getMockFilter();
        $filter2 = $this->getMockFilter();
        $expression = $this->getMockExpression();
        $filter->method('getExpression')->willReturn($this->getMockExpression());
        $filter->method('getName')->willReturn('IAmAFilter');
        $filter->method('getEntity')->willReturn('OfThisEntity');
        $filter2->method('getExpression')->willReturn($expression);
        $filter2->method('getName')->willReturn('IAmAFilter');
        $filter2->method('getEntity')->willReturn('OfThisOtherEntity');
        $appCtx->method('getFilters')->willReturn(array($filter,$filter2));

        $referenceFilter = new FilterReference($appCtx, $filter2->getEntity(), $filter2->getName());
        $expression = $referenceFilter->getExpression();
        $this->assertSame($filter2->getExpression(), $expression);

    }

    /**
     * @expectedException \Exception
     */
    public function testGetExpressionWithoutFilter () {

        $appCtx = $this->getApplicationContext();
        $referenceFilter = new FilterReference($appCtx, 'project', 'badFilterName');
        $referenceFilter->getExpression();

    }

}
