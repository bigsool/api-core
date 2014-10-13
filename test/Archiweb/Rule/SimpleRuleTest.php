<?php


namespace Archiweb\Rule;


use Archiweb\Model\User;
use Archiweb\TestCase;

class SimpleRuleTest extends TestCase {

    /**
     *
     */
    public function testShouldApply () {

        $called = false;

        $rule = new SimpleRule('isYourCompany', function () use (&$called) {

            $called = true;

        }, $this->getMockFilter());

        // shouldn't be called in a save context
        $rule->shouldApply($this->getSaveQueryContext(new User()));
        $this->assertFalse($called);

        // should be called in query context
        $rule->shouldApply($this->getFindQueryContext('User'));
        $this->assertTrue($called);

    }

    /**
     *
     */
    public function testListChildRules () {

        $filter = $this->getMockFilter();

        $rule = new SimpleRule('isYourCompany', $this->getCallable(), $filter);

        $this->assertSame([], $rule->listChildRules());

    }

    /**
     *
     */
    public function testGetName () {

        $filter = $this->getMockFilter();

        $name = 'isYourCompany';
        $rule = new SimpleRule($name, $this->getCallable(), $filter);

        $this->assertSame($name, $rule->getName());

    }

    /**
     *
     */
    public function testGetFilter () {

        $filter = $this->getMockFilter();

        $rule = new SimpleRule('name', $this->getCallable(), $filter);

        $this->assertSame($filter, $rule->getFilter());

    }

    /**
     *
     */
    public function testApply () {

        $filter = $this->getMockFilter();

        $rule = new SimpleRule('name', $this->getCallable(), $filter);
        $ctx = $this->getFindQueryContext('entity');
        $rule->apply($ctx);

        $this->assertContains($filter, $ctx->getFilters());

    }

} 