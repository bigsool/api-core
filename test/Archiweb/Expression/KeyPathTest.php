<?php


namespace Archiweb\Expression;


class KeyPathTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     */
    public function testGetValue()
    {

        $param = new KeyPath('user.company.storage');
        $this->assertEquals('user.company.storage', $param->getValue());

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidFormat()
    {
        new KeyPath('qwe/qwe');
    }

    /**
     *
     */
    public function testGetFilters()
    {
        // TODO: Implements the testGetFilters() method
    }

    /**
     *
     */
    public function testResolve()
    {
        $registry = $this->getMock('\Archiweb\Registry');
        $context = $this->getMock('\Archiweb\Context');

        $param = 'company.storage.url';

        $param1 = new Parameter($param);
        $resolve1 = $param1->resolve($registry, $context);

        $param2 = new Parameter($param);
        $resolve2 = $param2->resolve($registry, $context);

        $this->assertEquals('companyStorage.url', $resolve1);
        $this->assertEquals($resolve1, $resolve2);
    }

} 