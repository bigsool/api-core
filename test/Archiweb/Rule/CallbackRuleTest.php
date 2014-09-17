<?php


namespace Archiweb\Rule;


class CallbackRuleTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testShouldApply () {

        $ctx = $this->getMockBuilder('\Archiweb\ActionContext')
                    ->disableOriginalConstructor()
                    ->getMock();

        $mockRule = $this->getMock('\Archiweb\Rule\Rule');

        $rule = new CallbackRule('select', 'Company', 'isYourCompany', function () {
        }, []);

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

        $mockRule = $this->getMock('\Archiweb\Rule\Rule');

        $rule = new CallbackRule('select', 'Company', 'isYourCompany', function () {
        }, []);
        $this->assertEquals([], $rule->listChildRules());

        $rule = new CallbackRule('select', 'Company', 'isYourCompany', function () {
        }, [$mockRule]);
        $this->assertEquals([$mockRule], $rule->listChildRules());

    }

    /**
     *
     */
    public function testGetName () {

        $name = 'isYourCompany';
        $rule = new CallbackRule('select', 'Company', $name, function () {
        }, []);

        $this->assertEquals($name, $rule->getName());

    }

    /**
     *
     */
    public function testGetEntity () {

        $entity = 'Company';
        $rule = new CallbackRule('select', $entity, 'isYourCompany', function () {
        }, []);

        $this->assertEquals($entity, $rule->getEntity());

    }

}