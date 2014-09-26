<?php


namespace Archiweb\Rule;


use Archiweb\Field;
use Archiweb\TestCase;

class FieldRuleTest extends TestCase {

    /**
     *
     */
    public function testGetName () {

        $field = $this->getMockCompanyNameField();
        $mockRule = $this->getMockCompanyRule();

        $name = 'companyNameFieldRule';
        $rule = new FieldRule($field, $mockRule);

        $this->assertSame($name, $rule->getName());

    }

    /**
     * @return Field
     */
    protected function getMockCompanyNameField () {

        $field = $this->getMockField();
        $field->method('getEntity')->willReturn('company');
        $field->method('getName')->willReturn('name');

        return $field;

    }

    /**
     * @return Rule
     */
    protected function getMockCompanyRule () {

        $rule = $this->getMockRule();
        $rule->method('getEntity')->willReturn('company');

        return $rule;

    }

    /**
     * @expectedException \Exception
     */
    public function testIncompatibleRule () {

        $field = $this->getMockField();
        $field->method('getEntity')->willReturn('a');
        $field->method('getName')->willReturn('name');

        $rule = $this->getMockRule();
        $rule->method('getEntity')->willReturn('b');
        $rule->method('getCommand')->willReturn('SELECT');

        new FieldRule($field, $rule);

    }

    public function testGetRule () {

        $field = $this->getMockCompanyNameField();
        $mockRule = $this->getMockCompanyRule();
        $rule = new FieldRule($field, $mockRule);

        $this->assertSame($mockRule, $rule->getRule());

    }

    public function testGetField () {

        $field = $this->getMockCompanyNameField();
        $mockRule = $this->getMockCompanyRule();
        $rule = new FieldRule($field, $mockRule);

        $this->assertSame($field, $rule->getField());

    }

    public function testListChildRule () {

        $field = $this->getMockCompanyNameField();
        $mockRule = $this->getMockCompanyRule();
        $rule = new FieldRule($field, $mockRule);

        $this->assertSame([$mockRule], $rule->listChildRules());

    }

    public function testShouldApply () {

        $field = $this->getMockCompanyNameField();
        $mockRule = $this->getMockCompanyRule();
        $rule = new FieldRule($field, $mockRule);

        $qryCtx = $this->getFindQueryContext('Company');

        $this->assertTrue($rule->shouldApply($qryCtx));

    }

    public function testApply () {

        $field = $this->getMockCompanyNameField();
        $mockRule = $this->getMockCompanyRule();

        $called = false;

        $mockRule->method('apply')->will($this->returnCallback(function () use (&$called) {

            $called = true;

        }));

        $rule = new FieldRule($field, $mockRule);
        $rule->apply($this->getMockFindQueryContext());

        $this->assertTrue($called);

    }

} 