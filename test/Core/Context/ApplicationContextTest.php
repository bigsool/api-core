<?php


namespace Core\Context;


use Core\TestCase;

class ApplicationContextTest extends TestCase {

    public function testRuleManager () {

        $ctx = $this->getApplicationContext();
        $ruleMgr = $this->getMockRuleProcessor();

        $ctx->setRuleProcessor($ruleMgr);
        $this->assertSame($ruleMgr, $ctx->getRuleProcessor());

    }

    public function testProduct () {

        $ctx = $this->getApplicationContext();

        $this->assertSame('Core', $ctx->getProduct());

    }

    public function testLogger () {

        $ctx = $this->getApplicationContext();

        $logger = $ctx->getLogger();
        $this->assertInstanceOf('\Core\Logger\Logger', $logger);
        $this->assertSame($logger, $ctx->getLogger());

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

        $classMetadata = $this->getApplicationContext()->getClassMetadata('\Core\Model\TestCompany');

        $this->assertInstanceOf('\Doctrine\ORM\Mapping\ClassMetadata', $classMetadata);
        $this->assertSame('Core\Model\TestCompany', $classMetadata->getName());

    }

    public function testGetFilterByEntityAndName () {

        $ctx = $this->getApplicationContext();

        $filters[] = $filter = $this->getMockFilter();
        $filter->method('getEntity')->willReturn('TestCompany');
        $filter->method('getName')->willReturn('name');
        $ctx->addFilter($filter);
        $this->assertSame($filter, $ctx->getFilterByEntityAndName('TestCompany', 'name'));

    }

    /**
     * @expectedException \Exception
     */
    public function testGetFilterByEntityAndNameNotFound () {

        $this->getApplicationContext()->getFilterByEntityAndName('TestCompany', 'name');

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

        $this->assertSame($mockAction, $appCtx->getAction('module', 'name'));

    }

    /**
     * @expectedException \Exception
     */
    public function testDuplicatedAction () {

        $appCtx = $this->getApplicationContext();
        $mockAction = $this->getMockAction();
        $mockAction->method('getModule')->willReturn('module');
        $mockAction->method('getName')->willReturn('name');
        $appCtx->addAction($mockAction);
        $appCtx->addAction($mockAction);

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

        $action = $this->getMockAction();
        $action->method('getModule')->willReturn('module');

        $helper = new \stdClass();
        $appCtx->addHelper('module', 'helper1', $helper);
        $this->assertCount(1, $appCtx->getHelpers()['module']);
        $this->assertSame($helper, $appCtx->getHelpers()['module']['helper1']);

        $helper2 = new \stdClass();
        $appCtx->addHelper('module', 'helper2', $helper2);
        $this->assertCount(2, $appCtx->getHelpers()['module']);
        $this->assertSame($helper2, $appCtx->getHelper($action, 'helper2'));

        $appCtx->addHelper('module', 'helper2', new \stdClass());
        $this->assertCount(2, $appCtx->getHelpers()['module']);

    }

    /**
     * @expectedException \Exception
     */
    public function testHelperNotFound () {

        $action = $this->getMockAction();
        $this->getApplicationContext()->getHelper($action, 'qwe');

    }

    public function testRoutes () {

        $appCtx = $this->getApplicationContext();
        $this->assertInstanceOf('\Symfony\Component\Routing\RouteCollection', $appCtx->getRoutes());
        $this->assertSame(0, $appCtx->getRoutes()->count());

        $appCtx->addRoute('path', $this->getMockAction());
        $this->assertSame(1, $appCtx->getRoutes()->count());

    }

    public function testActionQueues () {

        $appCtx = $this->getApplicationContext();
        $successQueue = $appCtx->getOnSuccessActionQueue();
        $this->assertInstanceOf('\Core\ActionQueue', $successQueue);
        $this->assertEmpty($successQueue);
        $errorQueue = $appCtx->getOnErrorActionQueue();
        $this->assertInstanceOf('\Core\ActionQueue', $errorQueue);
        $this->assertEmpty($errorQueue);

        $successQueue->addAction($this->getMockAction());
        $this->assertCount(1, $successQueue);
        $this->assertEmpty($errorQueue);

        $errorQueue->addAction($this->getMockAction(), []);
        $successQueue->dequeue();
        $this->assertCount(1, $errorQueue);
        $this->assertEmpty($successQueue);

    }

}