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
        $mockRule = $this->getMockSelectCompanyRule();

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
    protected function getMockSelectCompanyRule () {

        $rule = $this->getMockRule();
        $rule->method('getEntity')->willReturn('company');
        $rule->method('getCommand')->willReturn('SELECT');

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

    /**
     *
     */
    public function testGetEntity () {

        $field = $this->getMockCompanyNameField();
        $mockRule = $this->getMockSelectCompanyRule();

        $rule = new FieldRule($field, $mockRule);

        $this->assertSame('company', $rule->getEntity());

    }

    /**
     *
     */
    public function testGetCommand () {

        $field = $this->getMockCompanyNameField();
        $mockRule = $this->getMockSelectCompanyRule();

        $rule = new FieldRule($field, $mockRule);

        $this->assertSame($mockRule->getCommand(), $rule->getCommand());

    }

    public function testGetRule () {

        $field = $this->getMockCompanyNameField();
        $mockRule = $this->getMockSelectCompanyRule();
        $rule = new FieldRule($field, $mockRule);

        $this->assertSame($mockRule, $rule->getRule());

    }

    public function testGetField () {

        $field = $this->getMockCompanyNameField();
        $mockRule = $this->getMockSelectCompanyRule();
        $rule = new FieldRule($field, $mockRule);

        $this->assertSame($field, $rule->getField());

    }

    public function testListChildRule () {

        $field = $this->getMockCompanyNameField();
        $mockRule = $this->getMockSelectCompanyRule();
        $rule = new FieldRule($field, $mockRule);

        $this->assertSame([$mockRule], $rule->listChildRules());

    }

    public function testShouldApply () {

        $field = $this->getMockCompanyNameField();
        $mockRule = $this->getMockSelectCompanyRule();
        $rule = new FieldRule($field, $mockRule);

        $qryCtx = $this->getFindQueryContext('Company');

        $this->assertTrue($rule->shouldApply($qryCtx));

    }

    public function testApply () {

        $field = $this->getMockCompanyNameField();
        $mockRule = $this->getMockSelectCompanyRule();

        $called = false;

        $mockRule->method('apply')->will($this->returnCallback(function () use (&$called) {

            $called = true;

        }));

        $rule = new FieldRule($field, $mockRule);
        $rule->apply($this->getMockQueryContext());

        $this->assertTrue($called);

    }

} 