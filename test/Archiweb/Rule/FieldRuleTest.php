<?php


namespace Archiweb\Rule;


use Archiweb\Context\FindQueryContext;
use Archiweb\Context\RequestContext;
use Archiweb\Context\SaveQueryContext;
use Archiweb\Field\Field;
use Archiweb\Field\KeyPath;
use Archiweb\Field\StarField;
use Archiweb\Model\Company;
use Archiweb\TestCase;

class FieldRuleTest extends TestCase {

    /**
     *
     */
    public function testGetName () {

        $rule = new FieldRule($this->getMockCompanyNameField(), $this->getMockFilter());

        $this->assertSame('companyNameFieldRule', $rule->getName());

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

        $appCtx = $this->getApplicationContext();
        $appCtx->addField($field = new Field('Company', 'name'));
        $appCtx->addField($starField = new StarField('Company'));
        $appCtx->addField(new Field('Company', 'city'));

        $rule = new FieldRule($field, $this->getMockFilter());
        $this->assertFalse($rule->shouldApply(new SaveQueryContext(new Company())));

        $reqCtx = new RequestContext();
        $reqCtx->setReturnedRootEntity('Company');

        $qryCtx = new FindQueryContext('Company', $reqCtx);
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

        $appCtx = $this->getApplicationContext();
        $appCtx->addField($field = new Field('Company', 'name'));

        $filter = $this->getMockFilter();

        $rule = new FieldRule($field, $filter);

        $reqCtx = new RequestContext();
        $reqCtx->setReturnedRootEntity('Company');
        $reqCtx->setReturnedKeyPaths([new KeyPath('name')]);

        $qryCtx = new FindQueryContext('Company', $reqCtx);
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

        (new FieldRule($this->getMockField(), $this->getMockFilter()))->apply(new SaveQueryContext(new Company()));

    }

} 