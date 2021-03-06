<?php


namespace Core\Parameter;


class UnsafeParameterTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testIsSafe () {

        $param = new UnsafeParameter('qwe', 'qwe', '');
        $this->assertFalse($param->isSafe());

    }

    /**
     *
     */
    public function testGetValue () {

        $obj = new \stdClass();
        $param = new UnsafeParameter($obj, $obj, '');
        $this->assertSame($obj, $param->getValue());

    }

    /**
     *
     */
    public function testPath () {

        $obj = new \stdClass();
        $param = new UnsafeParameter($obj, $obj, 'qwe');
        $this->assertSame('qwe', $param->getPath());

    }

} 