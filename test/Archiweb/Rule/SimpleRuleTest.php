<?php


namespace Archiweb\Rule;


use Archiweb\ActionContext;
use Archiweb\Context;

class SimpleRuleTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testShouldApply () {

        $ctxMock = $this->getMockBuilder('\Archiweb\ActionContext')
                        ->disableOriginalConstructor()
                        ->getMock();
        $ruleMock = $this->getMockBuilder('\Archiweb\Rule\Rule')
                         ->disableOriginalConstructor()
                         ->getMock();

        $filter = $this->getMockBuilder('\Archiweb\Filter\Filter')
                       ->disableOriginalConstructor()
                       ->getMock();

        $rule = new SimpleRule('select', 'Company', 'isYourCompany', $filter);

        // not rules already in the list to apply
        $_rule = clone $ruleMock;
        $_rule->method('listChildRules')->willReturn([]);
        $ctx = clone $ctxMock;
        $ctx->method('getRules')->willReturn([]);
        $this->assertTrue($rule->shouldApply($ctx));

        // tested rule already in the list to apply
        $_rule = clone $ruleMock;
        $_rule->method('listChildRules')->willReturn([]);
        $ctx = clone $ctxMock;
        $ctx->method('getRules')->willReturn([$rule]);
        $this->assertFalse($rule->shouldApply($ctx));

        // other rule already in the list to apply
        $_rule = clone $ruleMock;
        $_rule->method('listChildRules')->willReturn([]);
        $ctx = clone $ctxMock;
        $ctx->method('getRules')->willReturn([$_rule]);
        $this->assertTrue($rule->shouldApply($ctx));

        // other rule which contain tested rule already in the list to apply
        $_rule = clone $ruleMock;
        $_rule->method('listChildRules')->willReturn([$rule]);
        $ctx = clone $ctxMock;
        $ctx->method('getRules')->willReturn([$_rule]);
        $this->assertFalse($rule->shouldApply($ctx));

    }

    /**
     *
     */
    public function testListChildRules () {

        $filter = $this->getMockBuilder('\Archiweb\Filter\Filter')
                       ->disableOriginalConstructor()
                       ->getMock();

        $rule = new SimpleRule('select', 'Company', 'isYourCompany', $filter);

        $this->assertSame([], $rule->listChildRules());

    }

    /**
     *
     */
    public function testGetName () {

        $filter = $this->getMockBuilder('\Archiweb\Filter\Filter')
                       ->disableOriginalConstructor()
                       ->getMock();

        $name = 'isYourCompany';
        $rule = new SimpleRule('select', 'Company', $name, $filter);

        $this->assertSame($name, $rule->getName());

    }

    /**
     *
     */
    public function testGetEntity () {

        $filter = $this->getMockBuilder('\Archiweb\Filter\Filter')
                       ->disableOriginalConstructor()
                       ->getMock();

        $entity = 'Company';
        $rule = new SimpleRule('select', $entity, 'isYourCompany', $filter);

        $this->assertSame($entity, $rule->getEntity());

    }

    /**
     *
     */
    public function testGetFilter () {

        $filter = $this->getMockBuilder('\Archiweb\Filter\Filter')
                       ->disableOriginalConstructor()
                       ->getMock();

        $rule = new SimpleRule('select', 'entity', 'name', $filter);

        $this->assertSame($filter, $rule->getFilter());

    }

    /**
     *
     */
    public function testApply () {

        $filter = $this->getMockBuilder('\Archiweb\Filter\Filter')
                       ->disableOriginalConstructor()
                       ->getMock();

        $rule = new SimpleRule('command', 'entity', 'name', $filter);
        $context = new Context();
        $entityManager = $this->getMockBuilder('\Doctrine\ORM\EntityManager')
                              ->disableOriginalConstructor()
                              ->getMock();
        $context->setEntityManager($entityManager);
        $actionCtx = new ActionContext($context);
        $rule->apply($actionCtx);

        $this->assertContains($filter, $actionCtx->getFilters());

    }

    /**
     *
     */
    public function testGetCommand () {

        $filter = $this->getMockBuilder('\Archiweb\Filter\Filter')
                       ->disableOriginalConstructor()
                       ->getMock();

        $command = 'select';
        $rule = new SimpleRule($command, 'Company', 'isYourCompany', $filter);

        $this->assertSame($command, $rule->getCommand());

    }

} 