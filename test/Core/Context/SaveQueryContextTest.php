<?php


namespace Core\Context;


use Core\Model\Company;
use Core\TestCase;

class SaveQueryContextTest extends TestCase {

    /**
     * @expectedException \Exception
     */
    public function testInvalidModelType () {

        new SaveQueryContext('qwe');

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidModelClass () {

        new SaveQueryContext(new \stdClass());

    }

    public function testGetEntity () {

        $ctx = new SaveQueryContext(new Company());

        $this->assertSame('Company', $ctx->getEntity());

    }

} 