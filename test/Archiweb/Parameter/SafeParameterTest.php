<?php


namespace Archiweb\Parameter;


class SafeParameterTest extends \PHPUnit_Framework_TestCase {

    public function testIsSafe() {

        $param = new SafeParameter('qwe');
        $this->assertTrue($param->isSafe());

    }

    public function testGetValue() {

        $obj = new \stdClass();
        $param = new SafeParameter($obj);
        $this->assertSame($obj, $param->getValue());

    }

} 