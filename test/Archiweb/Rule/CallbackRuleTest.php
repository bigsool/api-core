<?php


namespace Archiweb\Rule;


class CallbackRuleTest extends \PHPUnit_Framework_TestCase {

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

        $rule = new CallbackRule('select', 'Company', 'isYourCompany', function () {
        }, []);

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

        $mockRule = $this->getMockBuilder('\Archiweb\Rule\Rule')
                         ->disableOriginalConstructor()
                         ->getMock();

        $rule = new CallbackRule('select', 'Company', 'isYourCompany', function () {
        }, []);
        $this->assertSame([], $rule->listChildRules());

        $rule = new CallbackRule('select', 'Company', 'isYourCompany', function () {
        }, [$mockRule]);
        $this->assertSame([$mockRule], $rule->listChildRules());

    }

    /**
     *
     */
    public function testGetName () {

        $name = 'isYourCompany';
        $rule = new CallbackRule('select', 'Company', $name, function () {
        }, []);

        $this->assertSame($name, $rule->getName());

    }

    /**
     *
     */
    public function testGetEntity () {

        $entity = 'Company';
        $rule = new CallbackRule('select', $entity, 'isYourCompany', function () {
        }, []);

        $this->assertSame($entity, $rule->getEntity());

    }

}