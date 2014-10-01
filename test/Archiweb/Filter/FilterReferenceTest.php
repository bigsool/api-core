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
        $expression = $this->getMockExpression();
        $filter->method('getExpression')->willReturn($expression);
        $appCtx->method('getFilters')->willReturn(array($filter));
        $appCtx->addFilter($filter);

        $referenceFilter = new FilterReference($appCtx, 'project', $filter->getName());
        $expression = $referenceFilter->getExpression();
        $this->assertSame($filter->getExpression(), $expression);

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
