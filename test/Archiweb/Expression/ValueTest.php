<?php


namespace Archiweb\Expression;


class ValueTest extends \PHPUnit_Framework_TestCase {

    /**
     *
     */
    public function testGetValue () {

        $tester = function ($value) {

            $val = new Value($value);
            $this->assertEquals($value, $val->getValue());
        };

        $tester(123);

        $tester('String');

        $tester(['A', 'R', 'R', 'A', 'Y']);

        $tester(new \stdClass());

    }

    /**
     *
     */
    public function testResolve () {

        $registry = $this->getMockBuilder('\Archiweb\Registry')
                         ->disableOriginalConstructor()
                         ->getMock();
        $context = $this->getMock('\Archiweb\Context');

        $tester = function ($value) use ($registry, $context) {

            $val = new Value($value);
            $this->assertEquals($value, $val->resolve($registry, $context));
        };

        $tester(123);

        $tester('String');

        $tester(['A', 'R', 'R', 'A', 'Y']);

        $tester(new \stdClass());

    }

} 