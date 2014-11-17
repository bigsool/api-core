<?php


namespace Core\Operator;


class MemberOfTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testToDQL () {

        $operator = new MemberOf();
        $this->assertEquals('MEMBER OF qwe', $operator->toDQL('qwe'));
    }

    /**
     * @expectedException \Exception
     */
    public function testInvalidFormat () {

        $operator = new MemberOf();
        $operator->toDQL();
    }

} 