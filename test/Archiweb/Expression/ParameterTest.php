<?php


namespace Archiweb\Expression;


class ParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     *
     */
    public function testGetValue()
    {

        $param = new Parameter(':company');
        $this->assertEquals(':company', $param->getValue());

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidFormat()
    {
        new Parameter('qwe');
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidType()
    {
        new Parameter(new \stdClass());
    }

    /**
     *
     */
    public function testResolve()
    {

        $registry = $this->getMock('\Archiweb\Registry');
        $context = $this->getMock('\Archiweb\Context');

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

} 