<?php


namespace Core\Util;


class ArrayExtra {

    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * @param array $array1
     * @param array $array2
     * @param array $array2,...
     *
     * @return array
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     */

    public static function array_merge_recursive_distinct (array &$array1, array &$array2) {

        $merged = static::_array_merge_recursive_distinct($array1, $array2);
        if (func_num_args() > 2) {
            for ($i = 2; $i < func_num_args(); ++$i) {
                $argI = func_get_arg($i);
                $merged = static::_array_merge_recursive_distinct($merged, $argI);
            }
        }

        return $merged;

    }

    protected static function _array_merge_recursive_distinct (array &$array1, array &$array2) {

        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset ($merged [$key]) && is_array($merged [$key])) {
                $merged [$key] = self::_array_merge_recursive_distinct($merged [$key], $value);
            }
            else {
                $merged [$key] = $value;
            }
        }

        return $merged;

    }

    /**
     * add the possibility to use a dot (.) to separate the different dimensions of an array
     * E.g. : $arr = []; magicalSet($arr,'a.b.c','abc'); <=> $arr['a']['b']['c'] = 'abc';
     *
     * @param array $array
     * @param mixed $key
     * @param mixed $value
     */
    public static function magicalSet (array &$array, $key, $value) {

        if (!is_scalar($key)) {
            throw new \RuntimeException('invalid key type');
        }

        $exploded = explode('.', $key);
        foreach ($exploded as $index => $key) {

            // it's not necessary to create an array for the last key
            if ($index + 1 == count($exploded)) {
                break;
            }

            if (!isset($array[$key])) {
                $array[$key] = [];
            }
            $array = &$array[$key];

        }

        $array[$key] = $value;

    }

    /**
     * add the possibility to use a dot (.) to separate the different dimensions of an array
     * E.g. : $arr['a']['b']['c'] = 'abc'; echo magicalGet($arr,'a.b.c'); <=> echo $arr['a']['b']['c'];
     *
     * @param array $array
     * @param mixed $key
     * @param mixed $isset out parameter in order to know if the key exists or not
     *
     * @return mixed
     */
    public static function magicalGet (array $array, $key, &$isset = NULL) {

        $exploded = explode('.', $key);
        $return = NULL;
        for ($i = 0; $i < count($exploded); ++$i) {
            $key = $exploded[$i];

            // if [{},{},{}...]
            if (!static::isAssociative($array)) {
                $return = [];
                $newKey = implode('.', array_slice($exploded, $i));

                // foreach {} in [{},{},{}...]
                foreach ($array as $subArray) {
                    if (!is_array($subArray)) {
                        continue;
                    }
                    $tmpGet = static::magicalGet($subArray, $newKey);
                    if (is_null($tmpGet)) {
                        continue;
                    }

                    // if not [{},{},{}...]
                    if (!is_array($tmpGet) || !static::isAssociative($tmpGet)) {
                        $return[] = $tmpGet;
                    }
                    // if [{},{},{}...]
                    elseif (is_array($tmpGet)) {
                        foreach ($tmpGet as $subTmpGet) {
                            $return[] = $subTmpGet;
                        }
                    }
                }
                continue;
            }

            if (!isset($array[$key])) {
                $isset = false;
                return NULL;
            }
            // it's not necessary to create an array for the last key
            if ($i + 1 == count($exploded)) {
                $return = $array[$key];
            }
            $array = $array[$key];
        }

        $isset = true;

        return $return;

    }

    /**
     * @param array $array
     *
     * @return bool
     */
    public static function isAssociative (array &$array) {

        return array_keys($array) !== range(0, count($array) - 1);

    }

}