<?php

namespace Archiweb;


class ActionContextTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testParams () {

        $ctx = new Context();

        $array = [$this->getParameterMock('a'), 'b' => $this->getParameterMock(2), $this->getParameterMock(['c'])];

        $ctx->setParams($array);

        $actionCtx = new ActionContext($ctx);

        $this->assertSame($array, $actionCtx->getParams());
        $this->assertSame($array[0], $actionCtx->getParam(0));
        $this->assertSame($array['b'], $actionCtx->getParam('b'));

    }

    /**
     * @param $value
     *
     * @return Parameter
     */
    protected function getParameterMock ($value) {

        $mock = $this->getMockBuilder('\Archiweb\Parameter\Parameter')
                     ->disableOriginalConstructor()
                     ->getMock();
        $mock->method('getValue')->willReturn($value);

        return $mock;

    }

    /**
     *
     */
    public function testImplementArrayAccess () {

        $ctx = new ActionContext(new Context());

        $this->assertInstanceOf('\ArrayAccess', $ctx);

        $array = ['a', 'b' => 2, ['c']];

        foreach ($array as $key => $value) {
            $ctx[$key] = $value;
        }


        foreach ($array as $key => $value) {
            $this->assertArrayHasKey($key, $ctx);
            $this->assertEquals($value, $ctx[$key]);
            unset($ctx[$key]);
            $this->assertArrayNotHasKey($key, $ctx);
        }

    }

    /**
     *
     */
    public function testRules () {

        // empty rule list
        $ctx = new ActionContext(new Context());
        $this->assertSame([], $ctx->getRules());

        // only one rule
        $rule = $this->getRuleMock();
        $ctx->addRule($rule);
        $this->assertSame([$rule], $ctx->getRules());

        // several rules
        $rules = [$this->getRuleMock(), $this->getRuleMock()];
        foreach ($rules as $r) {
            $ctx->addRule($r);
        }
        $rules[] = $rule;
        $this->assertSameSize($rules, $ctx->getRules());
        foreach ($rules as $r) {
            $this->assertContains($r, $ctx->getRules());
        }

    }

    /**
     * @return Rule
     */
    protected function getRuleMock () {

        return $this->getMockBuilder('\Archiweb\Rule\Rule')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     *
     */
    public function testFilters () {

        // empty rule list
        $ctx = new ActionContext(new Context());
        $this->assertSame([], $ctx->getFilters());

        // only one rule
        $filter = $this->getFilterMock();
        $ctx->addFilter($filter);
        $this->assertSame([$filter], $ctx->getFilters());

        // several rules
        $filters = [$this->getFilterMock(), $this->getFilterMock()];
        foreach ($filters as $f) {
            $ctx->addFilter($f);
        }
        $filters[] = $filter;
        $this->assertSameSize($filters, $ctx->getFilters());
        foreach ($filters as $f) {
            $this->assertContains($f, $ctx->getFilters());
        }

    }

    /**
     * @return Filter
     */
    protected function getFilterMock () {

        return $this->getMockBuilder('\Archiweb\Filter\Filter')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidRule () {

        $ctx = new ActionContext(new Context());
        $ctx->addRule('qwe');

    }

    public function testEntityManager () {

        $ctx = new Context();
        $em = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
                   ->disableOriginalConstructor()
                   ->getMock();
        $ctx->setEntityManager($em);

        $actionCtx = new ActionContext($ctx);

        $this->assertSame($em, $actionCtx->getEntityManager());

    }

    /**
     * @expectedException \Exception
     */
    public function testEntityManagerNotFound () {

        $ctx = new Context();
        (new ActionContext($ctx))->getEntityManager();

    }

} 