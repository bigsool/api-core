<?php


namespace Archiweb\Rule;


use Archiweb\Context\FindQueryContext;
use Archiweb\Expression\KeyPath;
use Archiweb\Field;
use Archiweb\Model\Company;
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
        $field->method('getEntity')->willReturn('Company');
        $field->method('getName')->willReturn('name');

        return $field;

    }

    /**
     * @return Rule
     */
    protected function getMockCompanyRule () {

        $rule = $this->getMockRule();
        $rule->method('getEntity')->willReturn('Company');

        return $rule;

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


        $field = new Field('Company', 'name');
        $mockRule = $this->getMockCompanyRule();
        $rule = new FieldRule($field, $mockRule);
        $appCtx = $this->getApplicationContext();
        $appCtx->addField($field);


        $qryCtx = new FindQueryContext($appCtx, 'Company');
        $qryCtx->addKeyPath(new KeyPath('name'));

        $this->assertTrue($rule->shouldApply($qryCtx));


        $qryCtx = new FindQueryContext($appCtx, 'User');
        $qryCtx->addKeyPath(new KeyPath('name'));

        $this->assertFalse($rule->shouldApply($qryCtx));


        $qryCtx = new FindQueryContext($appCtx, 'Company');
        $qryCtx->addKeyPath(new KeyPath('name'));

        $this->assertFalse($rule->shouldApply($qryCtx));

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidContextType () {

        $field = $this->getMockCompanyNameField();
        $mockRule = $this->getMockCompanyRule();
        $rule = new FieldRule($field, $mockRule);

        $rule->shouldApply($this->getSaveQueryContext(new Company()));

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