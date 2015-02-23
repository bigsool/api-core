<?php


namespace Core\Rule;


use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Context\SaveQueryContext;
use Core\Field\Field;
use Core\Field\KeyPath;
use Core\Field\StarField;
use Core\Model\TestCompany;
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
        $field = new Field('TestCompany', 'name');
        $starField = new StarField('TestCompany');

        $rule = new FieldRule($field, $this->getMockFilter());
        $this->assertFalse($rule->shouldApply(new SaveQueryContext(new TestCompany())));

        $reqCtx = new RequestContext();
        $reqCtx->setReturnedRootEntity('TestCompany');

        $reqUserCtx = new RequestContext();
        $reqUserCtx->setReturnedRootEntity('TestUser');
        $qryCtx = new FindQueryContext('TestUser', $reqUserCtx);
        $reqUserCtx->setReturnedKeyPaths([new KeyPath('name')]);
        $qryCtx->addKeyPath(new KeyPath('name'));
        $this->assertFalse($rule->shouldApply($qryCtx));

        $qryCtx = new FindQueryContext('TestCompany', $reqCtx);
        $reqCtx->setReturnedKeyPaths([new KeyPath('city')]);
        $qryCtx->addKeyPath(new KeyPath('city'));
        $this->assertFalse($rule->shouldApply($qryCtx));

        $reqCtx->setReturnedKeyPaths([new KeyPath('city'), new KeyPath('name')]);
        $qryCtx->addKeyPath(new KeyPath('name'));
        $this->assertTrue($rule->shouldApply($qryCtx));

        $rule = new FieldRule($starField, $this->getMockFilter());
        $this->assertTrue($rule->shouldApply($qryCtx));

    }

    public function testApply () {

        $this->getApplicationContext();
        $field = new Field('TestCompany', 'name');

        $filter = $this->getMockFilter();

        $rule = new FieldRule($field, $filter);

        $reqCtx = new RequestContext();
        $reqCtx->setReturnedRootEntity('TestCompany');
        $reqCtx->setReturnedKeyPaths([new KeyPath('name')]);

        $qryCtx = new FindQueryContext('TestCompany', $reqCtx);
        $qryCtx->addKeyPath(new KeyPath('name'));

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

        (new FieldRule($this->getMockField(), $this->getMockFilter()))->apply(new SaveQueryContext(new TestCompany()));

    }

} 