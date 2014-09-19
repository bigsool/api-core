<?php

namespace Archiweb;


use Doctrine\ORM\EntityManager;

class ActionContextTest extends \PHPUnit_Framework_TestCase {

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     *
     */
    public function testParams () {

        $ctx = $this->context;

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

        $ctx = new ActionContext($this->context);

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
        $ctx = new ActionContext($this->context);
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
        $ctx = new ActionContext($this->context);
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
     *
     */
    public function testFields () {

        // empty rule list
        $ctx = new ActionContext($this->context);
        $this->assertSame([], $ctx->getFields());

        // only one rule
        $field = $this->getFieldMock();
        $ctx->addField($field);
        $this->assertSame([$field], $ctx->getFields());

        // several rules
        $fields = [$this->getFieldMock(), $this->getFieldMock()];
        foreach ($fields as $f) {
            $ctx->addField($f);
        }
        $fields[] = $field;
        $this->assertSameSize($fields, $ctx->getFields());
        foreach ($fields as $f) {
            $this->assertContains($f, $ctx->getFields());
        }

    }

    /**
     * @return Field
     */
    protected function getFieldMock () {

        return $this->getMockBuilder('\Archiweb\Field')
                    ->disableOriginalConstructor()
                    ->getMock();

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidRule () {

        $ctx = new ActionContext($this->context);
        $ctx->addRule('qwe');

    }

    public function testEntityManager () {

        $actionCtx = new ActionContext($this->context);

        $this->assertSame($this->entityManager, $actionCtx->getEntityManager());

    }

    /**
     * @expectedException \Exception
     */
    public function testEntityManagerNotFound () {

        $ctx = new Context();
        (new ActionContext($ctx))->getEntityManager();

    }

    protected function setUp () {

        $this->context = new Context();
        $this->entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
                                    ->disableOriginalConstructor()
                                    ->getMock();
        $this->context->setEntityManager($this->entityManager);

    }

} 