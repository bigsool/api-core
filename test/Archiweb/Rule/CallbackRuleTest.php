<?php


namespace Archiweb\Rule;


use Archiweb\Context\QueryContext;
use Archiweb\TestCase;

class CallbackRuleTest extends TestCase {

    /**
     *
     */
    public function testShouldApply () {

        $ctxMock = $this->getMockQueryContext();

        $ruleMock = $this->getMockRule();

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

        $mockRule = $this->getMockRule();

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

    /**
     *
     */
    public function testGetCallback () {

        $fn = function () {
        };
        $rule = new CallbackRule('select', 'entity', 'name', $fn, []);

        $this->assertSame($fn, $rule->getCallback());

    }

    public function testApply () {

        $functionCalled = false;

        $rule = new CallbackRule('select', 'entity', 'name', function (QueryContext $ctx) use (&$functionCalled) {

            $functionCalled = true;

        }, []);

        $rule->apply($this->getMockQueryContext());

        $this->assertTrue($functionCalled);

    }

}