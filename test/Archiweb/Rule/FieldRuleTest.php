<?php


namespace Archiweb\Rule;


use Archiweb\Context\FindQueryContext;
use Archiweb\Expression\KeyPath;
use Archiweb\Field\Field;
use Archiweb\Field\StarField;
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
        $appCtx->addField(new Field('Company', 'owner'));
        $appCtx->addField(new Field('User', 'name'));
        $appCtx->addField(new StarField('Company'));
        $appCtx->addField(new StarField('User'));


        $qryCtx = new FindQueryContext($appCtx, 'Company');
        $qryCtx->addKeyPath(new KeyPath('name'));

        $this->assertTrue($rule->shouldApply($qryCtx));


        $qryCtx = new FindQueryContext($appCtx, 'User');
        $qryCtx->addKeyPath(new KeyPath('name'));

        $this->assertFalse($rule->shouldApply($qryCtx));


        $qryCtx = new FindQueryContext($appCtx, 'Company');
        $qryCtx->addKeyPath(new KeyPath('owner'));

        $this->assertFalse($rule->shouldApply($qryCtx));


        $qryCtx = new FindQueryContext($appCtx, 'Company');
        $qryCtx->addKeyPath(new KeyPath('*'));

        $this->assertTrue($rule->shouldApply($qryCtx));

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