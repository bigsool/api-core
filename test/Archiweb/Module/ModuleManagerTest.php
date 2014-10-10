<?php


namespace Archiweb\Module;


use Archiweb\Context\ApplicationContext;
use Archiweb\TestCase;

class ModuleManagerTest extends TestCase {

    public function testLoad () {

        $fn = function (&$bool, ApplicationContext &$ctx) {

            return function (ApplicationContext $context) use (&$bool, &$ctx) {

                $bool = true;
                $this->assertSame($ctx, $context);

            };

        };

        $loadFieldsCalled = false;
        $loadFiltersCalled = false;
        $loadRulesCalled = false;
        $loadActionsCalled = false;
        $loadRoutesCalled = false;
        $appCtx = $this->getApplicationContext();

        /**
         * @var ModuleManager $moduleManager
         */
        $moduleManager = $this->getMockForAbstractClass('\Archiweb\Module\ModuleManager');
        $moduleManager->method('loadFields')->will($this->returnCallback($fn($loadFieldsCalled, $appCtx)));
        $moduleManager->method('loadFilters')->will($this->returnCallback($fn($loadFiltersCalled, $appCtx)));
        $moduleManager->method('loadRules')->will($this->returnCallback($fn($loadRulesCalled, $appCtx)));
        $moduleManager->method('loadActions')->will($this->returnCallback($fn($loadActionsCalled, $appCtx)));
        $moduleManager->method('loadRoutes')->will($this->returnCallback($fn($loadRoutesCalled, $appCtx)));

        $moduleManager->load($appCtx);

        $this->assertTrue($loadFieldsCalled);
        $this->assertTrue($loadFiltersCalled);
        $this->assertTrue($loadRulesCalled);
        $this->assertTrue($loadActionsCalled);
        $this->assertTrue($loadRoutesCalled);

    }

} 