<?php


namespace Archiweb\Context;


use Archiweb\TestCase;

class ApplicationContextTest extends TestCase {

    public function testRuleManager () {

        $ctx = new ApplicationContext();
        $ruleMgr = $this->getMockRuleManager();

        $ctx->setRuleManager($ruleMgr);
        $this->assertSame($ruleMgr, $ctx->getRuleManager());

    }

    public function testEntityManager () {

        $ctx = new ApplicationContext();
        $em = $this->getMockEntityManager();

        $getEM = new \ReflectionMethod($ctx, 'getEntityManager');
        $getEM->setAccessible(true);

        $ctx->setEntityManager($em);

        $called = false;
        $receiver = $this->getMockEntityManagerReceiver();
        $receiver->method('setEntityManager')->will($this->returnCallback(function ($entityManager) use (
            $em, &$called
        ) {

            $this->assertSame($em, $entityManager);
            $called = true;

        }));

        $ctx->getEntityManager($receiver);

    }

} 