<?php


namespace Archiweb\Rule;


class SimpleRuleTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testShouldApply () {

        $ctx = $this->getMockBuilder('\Archiweb\ActionContext')
                    ->disableOriginalConstructor()
                    ->getMock();
        $mockRule = $this->getMock('\Archiweb\Rule\Rule');

        $filter = $this->getMockBuilder('\Archiweb\Filter\Filter')
                    ->disableOriginalConstructor()
                    ->getMock();

        $rule = new SimpleRule('select', 'Company', 'isYourCompany', $filter);

        // not rules already in the list to apply
        $mockRule->method('listChildRules')->willReturn([]);
        $ctx->method('getRules')->willReturn([]);
        $this->assertTrue($rule->shouldApply($ctx));

        // tested rule already in the list to apply
        $mockRule->method('listChildRules')->willReturn([]);
        $ctx->method('getRules')->willReturn([$rule]);
        $this->assertFalse($rule->shouldApply($ctx));

        // other rule already in the list to apply
        $mockRule->method('listChildRules')->willReturn([]);
        $ctx->method('getRules')->willReturn([$mockRule]);
        $this->assertTrue($rule->shouldApply($ctx));

        // other rule which contain tested rule already in the list to apply
        $mockRule->method('listChildRules')->willReturn([$rule]);
        $ctx->method('getRules')->willReturn([$mockRule]);
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

        $this->assertEquals([], $rule->listChildRules());

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

        $this->assertEquals($name, $rule->getName());

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

        $this->assertEquals($entity, $rule->getEntity());

    }

} 