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

    public function testMagicalGet () {

        $array = [
            'A' => 'a',
            'B' => [
                'C' => 'bc'
            ],
            'D' => [
                [
                    'E' => 'de1'
                ],
                [
                    'E' => 'de2'
                ]
            ],
            'F' => [
                [],
                1
            ],
            'I' => [
            ],
        ];

        $this->assertSame($array['A'], ArrayExtra::magicalGet($array, 'A'));
        $this->assertSame($array['B']['C'], ArrayExtra::magicalGet($array, 'B.C'));
        $this->assertSame([$array['D'][0]['E'], $array['D'][1]['E']], ArrayExtra::magicalGet($array, 'D.E'));
        $this->assertSame([], ArrayExtra::magicalGet($array, 'F.G'));
        $this->assertSame(NULL, ArrayExtra::magicalGet($array, 'H'));
        $this->assertSame([], ArrayExtra::magicalGet($array, 'I'));

    }

}