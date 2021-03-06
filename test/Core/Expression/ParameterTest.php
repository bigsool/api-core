<?php


namespace Core\Expression;


use Core\Model\Company;
use Core\Model\TestCompany;
use Core\Model\TestUser;
use Core\TestCase;

class ParameterTest extends TestCase {

    /**
     *
     */
    public function testGetValue () {

        $param = new Parameter(':company');
        $this->assertEquals(':company', $param->getValue());

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidFormat () {

        new Parameter('qwe');
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidType () {

        new Parameter(new \stdClass());
    }

    /**
     *
     */
    public function testResolve () {

        $registry = $this->getRegistry();
        $context = $this->getFindQueryContext('TestCompany');
        $context->setParams(['company' => new Company()]);

        $param = ':company';

        $param1 = new Parameter($param);
        $resolve1 = $param1->resolve($registry, $context);

        $param2 = new Parameter($param);
        $resolve2 = $param2->resolve($registry, $context);

        $this->assertInternalType('string', $resolve1);
        $this->assertInternalType('string', $resolve2);
        $this->assertStringStartsWith($param, $resolve1);
        $this->assertStringStartsWith($param, $resolve2);
        $this->assertNotEquals($resolve1, $resolve2);

    }

    /**
     *
     */
    public function testGetRealName () {

        $registry = $this->getRegistry();
        $context = $this->getFindQueryContext('TestCompany');
        $context->setParams(['company' => new Company()]);

        $param = ':company';

        $param1 = new Parameter($param);
        $resolve1 = $param1->resolve($registry, $context);
        $this->assertSame($resolve1, $param1->getRealName());

    }

    /**
     * @expectedException \Exception
     */
    public function testGetRealNameBeforeResolve () {

        $context = $this->getFindQueryContext('TestCompany');
        $context->setParams(['company' => new Company()]);

        $param = ':company';

        $param1 = new Parameter($param);
        $param1->getRealName();

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidContext () {

        $registry = $this->getRegistry();
        $context = $this->getSaveQueryContext(new TestUser());

        $param = ':user';

        $param1 = new Parameter($param);
        $param1->resolve($registry, $context);

    }

    /**
     * @expectedException \Exception
     */
    public function testParameterNotFound () {

        $registry = $this->getRegistry();
        $context = $this->getFindQueryContext('TestUser');

        $param = ':user';

        $param1 = new Parameter($param);
        $param1->resolve($registry, $context);

    }

}