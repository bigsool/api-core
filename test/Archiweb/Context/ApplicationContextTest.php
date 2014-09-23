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

        $ctx->setEntityManager($em);
        $this->assertSame($em, $ctx->getEntityManager());

    }

} 