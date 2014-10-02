<?php


namespace Archiweb\Rule;


use Archiweb\Context\QueryContext;
use Archiweb\TestCase;

class CallbackRuleTest extends TestCase {

    /**
     *
     */
    public function testShouldApply () {

        $called = false;

        $rule = new CallbackRule('isYourCompany', function () use (&$called) {

            $called = true;

        }, $this->getCallable(), []);

        $rule->shouldApply($this->getMockQueryContext());

        $this->assertTrue($called);

    }

    /**
     *
     */
    public function testListChildRules () {

        $mockRule = $this->getMockRule();

        $rule = new CallbackRule('isYourCompany', $this->getCallable(), $this->getCallable(), []);
        $this->assertSame([], $rule->listChildRules());

        $rule = new CallbackRule('isYourCompany', $this->getCallable(), $this->getCallable(), [$mockRule]);
        $this->assertSame([$mockRule], $rule->listChildRules());

    }

    /**
     *
     */
    public function testGetName () {

        $name = 'isYourCompany';
        $rule = new CallbackRule($name, $this->getCallable(), $this->getCallable(), []);

        $this->assertSame($name, $rule->getName());

    }

    /**
     *
     */
    public function testGetCallback () {

        $fn = $this->getCallable();
        $rule = new CallbackRule('name', $this->getCallable(), $fn, []);

        $this->assertSame($fn, $rule->getCallback());

    }

    public function testApply () {

        $functionCalled = false;

        $rule = new CallbackRule('name', $this->getCallable(), function (QueryContext $ctx) use (&$functionCalled) {

            $functionCalled = true;

        }, []);

        $rule->apply($this->getMockFindQueryContext());

        $this->assertTrue($functionCalled);

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidChildList () {

        new CallbackRule('name', $this->getCallable(), $this->getCallable(), ['qwe']);

    }

}