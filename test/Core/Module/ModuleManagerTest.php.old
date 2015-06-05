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

        $createModuleFiltersCalled = false;
        $createRulesCalled = false;
        $createActionsCalled = false;
        $createModuleEntitiesCalled = false;
        $appCtx = $this->getApplicationContext();

        /**
         * @var ModuleManager $moduleManager
         */
        $moduleManager =
            $this->getMockModuleManager(['createModuleFilters',
                                         'createRules',
                                         'createActions',
                                         'createModuleEntities'
                                        ]);
        $moduleManager->method('createModuleFilters')->will($this->returnCallback($fn($createModuleFiltersCalled,
                                                                                      $appCtx)));
        $moduleManager->method('createRules')->will($this->returnCallback($fn($createRulesCalled, $appCtx)));
        $moduleManager->method('createActions')->will($this->returnCallback($fn($createActionsCalled, $appCtx)));
        $moduleManager->method('createModuleEntities')->will($this->returnCallback($fn($createModuleEntitiesCalled,
                                                                                       $appCtx)));

        $moduleManager->load($appCtx);

        $this->assertTrue($createModuleFiltersCalled);
        $this->assertTrue($createRulesCalled);
        $this->assertTrue($createActionsCalled);
        $this->assertTrue($createModuleEntitiesCalled);

    }

}