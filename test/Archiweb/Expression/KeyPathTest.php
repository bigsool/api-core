<?php


namespace Archiweb\Expression;


class KeyPathTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testGetValue () {

        $param = new KeyPath('user.company.storage');
        $this->assertEquals('user.company.storage', $param->getValue());

    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidFormat () {

        new KeyPath('qwe/qwe');
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidType () {

        new KeyPath(new \stdClass());
    }

    /**
     *
     */
    public function testGetFilters () {
        // TODO: Implements the testGetFilters() method
    }

    /**
     *
     */
    public function testResolve () {

        $registry = $this->getMockBuilder('\Archiweb\Registry')
                         ->disableOriginalConstructor()
                         ->getMock();
        $context = $this->getMock('\Archiweb\Context');

        $param = 'company.storage.url';

        $param1 = new KeyPath($param);
        $resolve1 = $param1->resolve($registry, $context);

        $param2 = new KeyPath($param);
        $resolve2 = $param2->resolve($registry, $context);

        $this->assertEquals('companyStorage.url', $resolve1);
        $this->assertEquals($resolve1, $resolve2);
    }

} 