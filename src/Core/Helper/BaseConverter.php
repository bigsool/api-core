<?php
/**
 * Created by PhpStorm.
 * User: bigsool
 * Date: 24/06/15
 * Time: 16:08
 */

namespace Core\Helper;


class BaseConverter {

    /**
     * Perfect conversion base 10 to base 16
     *
     * @param string $dec Number in base 10
     *
     * @return string Number in base 16
     */
    public static function dec2hex ($dec) {

        $tab = ['0000' => '0',
                '0001' => '1',
                '0010' => '2',
                '0011' => '3'
                ,
                '0100' => '4',
                '0101' => '5',
                '0110' => '6',
                '0111' => '7'
                ,
                '1000' => '8',
                '1001' => '9',
                '1010' => 'a',
                '1011' => 'b'
                ,
                '1100' => 'c',
                '1101' => 'd',
                '1110' => 'e',
                '1111' => 'f'
        ];

        $bin = static::dec2bin($dec);
        $len = strlen($bin);
        $bin2 = str_pad($bin, ceil($len / 4) * 4, '0', STR_PAD_LEFT);

        $hex = '';
        for ($i = 0; $i < $len; $i += 4) {
            $hex .= $tab[substr($bin2, $i, 4)];
        }

        return $hex;
    }

    /**
     * Perfect conversion base 10 to base 2
     *
     * @param string $dec Number in base 10
     *
     * @return string Number in base 2
     */
    public static function dec2bin ($dec) {

        $bin = '';
        while ($dec) {
            $m = bcmod($dec, 2);
            $dec = bcdiv($dec, 2, 0);
            $bin .= abs($m);
        }

        return strrev($bin);
    }

    /**
     * Perfect conversion base 16 to base 10
     *
     * @param string $hex Number in base 16
     *
     * @return string Number in base 10
     */
    public static function hex2dec ($hex) {

        $len = strlen($hex);
        $dec = 0;
        for ($i = 1; $i <= $len; $i++) {
            $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))), 0);
        }

        return $dec;

    }

}
