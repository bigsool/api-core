<?php


namespace Core\Module;


use Core\Context\ApplicationContext;
use Core\TestCase;

class ModuleManagerTest extends TestCase {

    public function testLoad () {

        $fn = function (&$bool, ApplicationContext &$ctx) {

            return function (ApplicationContext $context) use (&$bool, &$ctx) {

                $bool = true;
                $this->assertSame($ctx, $context);

            };

        };

        $loadFiltersCalled = false;
        $loadRulesCalled = false;
        $loadActionsCalled = false;
        $loadHelpersCalled = false;
        $appCtx = $this->getApplicationContext();

        /**
         * @var ModuleManager $moduleManager
         */
        $moduleManager = $this->getMockForAbstractClass('\Core\Module\ModuleManager');
        $moduleManager->method('loadFilters')->will($this->returnCallback($fn($loadFiltersCalled, $appCtx)));
        $moduleManager->method('loadRules')->will($this->returnCallback($fn($loadRulesCalled, $appCtx)));
        $moduleManager->method('loadActions')->will($this->returnCallback($fn($loadActionsCalled, $appCtx)));
        $moduleManager->method('loadHelpers')->will($this->returnCallback($fn($loadHelpersCalled, $appCtx)));

        $moduleManager->load($appCtx);

        $this->assertTrue($loadFiltersCalled);
        $this->assertTrue($loadRulesCalled);
        $this->assertTrue($loadActionsCalled);
        $this->assertTrue($loadHelpersCalled);

    }

    public function testAddRoute () {

        $moduleManager = $this->getMockForAbstractClass('\Core\Module\ModuleManager');
        $moduleManager->addRoute('/user/create', 'UserCreate');

        $routes = ApplicationContext::getInstance()->getRoutes()->all();
        $route = $routes['UserCreate'];
        $this->assertSame('/user/create', $route->getPath());

    }

}