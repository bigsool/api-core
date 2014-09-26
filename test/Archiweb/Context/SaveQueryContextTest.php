<?php


namespace Archiweb\Context;


use Archiweb\Model\Company;
use Archiweb\TestCase;

class SaveQueryContextTest extends TestCase {

    /**
     * @expectedException \Exception
     */
    public function testInvalidModelType () {

        new SaveQueryContext($this->getApplicationContext(), 'qwe');

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidModelClass () {

        new SaveQueryContext($this->getApplicationContext(), new \stdClass());

    }

    public function testGetApplication () {

        $appCtx = $this->getApplicationContext();
        $ctx = new SaveQueryContext($appCtx, new Company());

        $this->assertSame($appCtx, $ctx->getApplicationContext());

    }

    public function testGetEntity () {

        $ctx = new SaveQueryContext($this->getApplicationContext(), new Company());

        $this->assertSame('Company', $ctx->getEntity());

    }

} 