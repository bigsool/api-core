<?php


namespace Core\Context;


use Core\TestCase;
use Symfony\Component\Routing\Route;

class ApplicationContextTest extends TestCase {

    public function testRuleManager () {

        $ctx = $this->getApplicationContext();
        $ruleMgr = $this->getMockRuleProcessor();

        $ctx->setRuleProcessor($ruleMgr);
        $this->assertSame($ruleMgr, $ctx->getRuleProcessor());

    }

    public function testFilters () {

        $ctx = $this->getApplicationContext();
        $filters = [$this->getMockFilter(), $this->getMockFilter(), $this->getMockFilter()];

        $this->assertSame([], $ctx->getFilters());

        $ctx->addFilter($filters[0]);
        $this->assertSame([$filters[0]], $ctx->getFilters());

        $ctx->addFilter($filters[1]);
        $ctx->addFilter($filters[2]);
        $this->assertSame($filters, $ctx->getFilters());

    }

    public function testFields () {

        $ctx = $this->getApplicationContext();
        $fields = [$this->getMockField(), $this->getMockField(), $this->getMockField()];

        $this->assertSame([], $ctx->getFields());

        $ctx->addField($fields[0]);
        $this->assertSame([$fields[0]], $ctx->getFields());

        $ctx->addField($fields[1]);
        $ctx->addField($fields[2]);
        $this->assertSame($fields, $ctx->getFields());

    }

    public function testRules () {

        $ctx = $this->getApplicationContext();
        $rules = [$this->getMockRule(), $this->getMockRule(), $this->getMockRule()];

        $this->assertSame([], $ctx->getRules());

        $ctx->addRule($rules[0]);
        $this->assertSame([$rules[0]], $ctx->getRules());

        $ctx->addRule($rules[1]);
        $ctx->addRule($rules[2]);
        $this->assertSame($rules, $ctx->getRules());

    }

    public function testGetNewRegistry () {

        $registry = $this->getApplicationContext()->getNewRegistry();

        $this->assertInstanceOf('\Core\Registry', $registry);

    }

    public function testGetClassMetadata () {

        $classMetadata = $this->getApplicationContext()->getClassMetadata('\Core\Model\Company');

        $this->assertInstanceOf('\Doctrine\ORM\Mapping\ClassMetadata', $classMetadata);
        $this->assertSame('Core\Model\Company', $classMetadata->getName());

    }

    public function testGetFieldsByEntity () {

        $ctx = $this->getApplicationContext();

        $this->assertEmpty($ctx->getFieldsByEntity('Company'));

        $fields[] = $field = $this->getMockField();
        $field->method('getEntity')->willReturn('Company');
        $ctx->addField($field);
        $this->assertSame($fields, $ctx->getFieldsByEntity('Company'));

        $field = $this->getMockField();
        $field->method('getEntity')->willReturn('Product');
        $ctx->addField($field);
        $this->assertSame($fields, $ctx->getFieldsByEntity('Company'));
        $this->assertSame([$field], $ctx->getFieldsByEntity('Product'));

    }

    public function testGetFieldByEntityAndName () {

        $ctx = $this->getApplicationContext();

        $fields[] = $field = $this->getMockField();
        $field->method('getEntity')->willReturn('Company');
        $field->method('getName')->willReturn('name');
        $ctx->addField($field);
        $this->assertSame($field, $ctx->getFieldByEntityAndName('Company', 'name'));

    }

    public function testGetFilterByEntityAndName () {

        $ctx = $this->getApplicationContext();

        $filters[] = $filter = $this->getMockFilter();
        $filter->method('getEntity')->willReturn('Company');
        $filter->method('getName')->willReturn('name');
        $ctx->addFilter($filter);
        $this->assertSame($filter, $ctx->getFilterByEntityAndName('Company', 'name'));

    }

    /**
     * @expectedException \Exception
     */
    public function testGetFieldByEntityAndNameNotFound () {

        $this->getApplicationContext()->getFieldByEntityAndName('Company', 'name');

    }

    /**
     * @expectedException \Exception
     */
    public function testGetFilterByEntityAndNameNotFound () {

        $this->getApplicationContext()->getFilterByEntityAndName('Company', 'name');

    }

    public function testAction () {

        $appCtx = $this->getApplicationContext();
        $this->assertInternalType('array', $appCtx->getActions());
        $this->assertCount(0, $appCtx->getActions());

        $mockAction = $this->getMockAction();
        $mockAction->method('getModule')->willReturn('module');
        $mockAction->method('getName')->willReturn('name');
        $appCtx->addAction($mockAction);
        $this->assertCount(1, $appCtx->getActions());
        $this->assertSame($mockAction, $appCtx->getActions()[0]);

        $mockAction2 = $this->getMockAction();
        $appCtx->addAction($mockAction2);
        $mockAction2->method('getModule')->willReturn('module');
        $mockAction2->method('getName')->willReturn('name2');
        $this->assertCount(2, $appCtx->getActions());
        $this->assertSame($mockAction2, $appCtx->getActions()[1]);

        $appCtx->addAction($mockAction2);
        $this->assertCount(2, $appCtx->getActions());

        $this->assertSame($mockAction, $appCtx->getAction('module', 'name'));

    }

    /**
     * @expectedException \Exception
     */
    public function testActionNotFound () {

        $this->getApplicationContext()->getAction('qwe', 'qwe');

    }

    public function testHelper () {

        $appCtx = $this->getApplicationContext();
        $this->assertInternalType('array', $appCtx->getHelpers());
        $this->assertCount(0, $appCtx->getHelpers());

        $helper = new \stdClass();
        $appCtx->addHelper('helper1', $helper);
        $this->assertCount(1, $appCtx->getHelpers());
        $this->assertSame($helper, $appCtx->getHelpers()['helper1']);

        $helper2 = new \stdClass();
        $appCtx->addHelper('helper2', $helper2);
        $this->assertCount(2, $appCtx->getHelpers());
        $this->assertSame($helper2, $appCtx->getHelper('helper2'));

        $appCtx->addHelper('helper2', new \stdClass());
        $this->assertCount(2, $appCtx->getHelpers());

    }

    /**
     * @expectedException \Exception
     */
    public function testHelperNotFound () {

        $this->getApplicationContext()->getHelper('qwe');

    }

    public function testRoutes () {

        $appCtx = $this->getApplicationContext();
        $this->assertInstanceOf('\Symfony\Component\Routing\RouteCollection', $appCtx->getRoutes());
        $this->assertSame(0, $appCtx->getRoutes()->count());

        $route1 = new Route('path');
        $appCtx->addRoute('route1', $route1);
        $this->assertSame(1, $appCtx->getRoutes()->count());

    }

} 