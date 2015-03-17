<?php


namespace Core\Util;


use Core\TestCase;

class ArrayExtraTest extends TestCase {

    public function testArray_merge_recursive_distinct () {

        $first = ['a' => 1, 'b' => [0 => 2, 1 => 'deux'], 'c' => [3, 'trois'], 'd' => 'quatre'];
        $second = ['a' => 'un', 'b' => [1 => 'II', 2 => 0b10], 'c' => 'III', 'e' => [5]];
        $expected = ['a' => 'un', 'b' => [2, 'II', 0b10], 'c' => 'III', 'd' => 'quatre', 'e' => [5]];
        $this->assertSame($expected, ArrayExtra::array_merge_recursive_distinct($first, $second));

    }

}