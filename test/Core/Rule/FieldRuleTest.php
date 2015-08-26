<?php


namespace Core\Rule;


use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Context\SaveQueryContext;
use Core\Field\Field;
use Core\Field\RelativeField;
use Core\Field\StarField;
use Core\Model\TestCompany;
use Core\Model\TestUser;
use Core\TestCase;

class FieldRuleTest extends TestCase {

    /**
     *
     */
    public function testGetName () {

        $rule = new FieldRule($this->getMockCompanyNameField(), $this->getMockFilter());

        $this->assertSame('testCompanyNameFieldRule', $rule->getName());

    }

    /**
     * @return Field
     */
    protected function getMockCompanyNameField () {

        $field = $this->getMockField();
        $field->method('getEntity')->willReturn('TestCompany');
        $field->method('getName')->willReturn('name');

        return $field;

    }

    public function testGetField () {

        $field = $this->getMockCompanyNameField();
        $rule = new FieldRule($field, $this->getMockFilter());

        $this->assertSame($field, $rule->getField());

    }

    public function testGetFilter () {

        $filter = $this->getMockFilter();
        $rule = new FieldRule($this->getMockCompanyNameField(), $filter);

        $this->assertSame($filter, $rule->getFilter());

    }

    public function testListChildRule () {

        $field = $this->getMockCompanyNameField();
        $mockFilter = $this->getMockFilter();
        $rule = new FieldRule($field, $mockFilter);

        $this->assertSame([], $rule->listChildRules());

    }

    public function testShouldApply () {

        $this->getApplicationContext();
        $field = new Field('TestUser', 'firstName');
        $starField = new StarField('TestUser');

        $rule = new FieldRule($field, $this->getMockFilter());
        $this->assertFalse($rule->shouldApply(new SaveQueryContext(new TestUser())));

        $reqCtx = new RequestContext();

        $reqUserCtx = new RequestContext();
        $qryCtx = new FindQueryContext('TestCredential', $reqUserCtx);
        $reqUserCtx->setReturnedFields([new RelativeField('firstName')]);
        $qryCtx->addField(new RelativeField('firstName'));
        $this->assertFalse($rule->shouldApply($qryCtx));

        $qryCtx = new FindQueryContext('TestUser', $reqCtx);
        $reqCtx->setReturnedFields([new RelativeField('lastName')]);
        $qryCtx->addField(new RelativeField('lastName'));
        $this->assertFalse($rule->shouldApply($qryCtx));

        $reqCtx->setReturnedFields([new RelativeField('lastName'), new RelativeField('firstName')]);
        $qryCtx->addField(new RelativeField('firstName'));
        $this->assertTrue($rule->shouldApply($qryCtx));

        $rule = new FieldRule($starField, $this->getMockFilter());
        $this->assertTrue($rule->shouldApply($qryCtx));

    }

    public function testApply () {

        $this->getApplicationContext();
        $field = new Field('TestUser', 'firstName');

        $filter = $this->getMockFilter();

        $rule = new FieldRule($field, $filter);

        $reqCtx = new RequestContext();
        $reqCtx->setReturnedFields([new RelativeField('firstName')]);

        $qryCtx = new FindQueryContext('TestUser', $reqCtx);
        $qryCtx->addField(new RelativeField('firstName'));

        $filters = $qryCtx->getFilters();
        $this->assertCount(0, $filters);

        $rule->apply($qryCtx);

        $filters = $qryCtx->getFilters();
        $this->assertCount(1, $filters);
        $this->assertContains($filter, $filters);

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidContextApply () {

        (new FieldRule($this->getMockField(), $this->getMockFilter()))->apply(new SaveQueryContext(new TestUser()));

    }

} 