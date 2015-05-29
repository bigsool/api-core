<?php


namespace Core\Helper\AggregatedModuleEntity;


use Core\Field\RelativeField;
use Core\Module\ModelAspect;

class Helper {

    /**
     * @param string        $prefix
     * @param ModelAspect[] $modelAspects
     *
     * @return RelativeField|null
     */
    public static function getRelativeFieldForPrefix ($prefix, $modelAspects) {

        foreach ($modelAspects as $modelAspect) {
            if ($modelAspect->getPrefix() && $modelAspect->getPrefix() == $prefix) {
                return $modelAspect->getRelativeField();
            }
        }

        return NULL;

    }

    /**
     * @param array $keys
     * @param array $data
     *
     * @return array
     */

    public static function buildArrayWithKeys ($keys, $data) {

        $tab = [];

        if (count($keys) == 1) {
            $tab[$keys[0]] = $data;
        }
        else {
            $tab[$keys[0]] = static::buildArrayWithKeys(array_slice($keys, 1, count($keys)), $data);
        }

        return $tab;

    }

    /**
     * @param array $keysToRemove
     * @param array $data
     *
     * @return array
     */

    public static function removeKeysFromArray ($keysToRemove, $data) {

        $newData = [];

        if (is_array($data)) {

            foreach ($data as $key => $value) {

                if (count($keysToRemove) == 1 && $keysToRemove[0] == $key) {
                    continue;
                }
                $newData[$key] =
                    static::removeKeysFromArray(array_slice($keysToRemove, 1, count($keysToRemove)), $value);

            }

        }
        else {

            $newData = $data;

        }

        return $newData;

    }

    /**
     * Replaces the value at $explodedPrefix with $finalValue in $params
     *
     * @param array $params
     * @param array $explodedPrefix
     * @param mixed $finalValue
     */
    public static function setFinalValue (&$params, $explodedPrefix, $finalValue) {


        $currentPrefix = $explodedPrefix[0];

        if (count($explodedPrefix) == 1) {
            $params[$currentPrefix] = $finalValue;
        }
        else {
            array_splice($explodedPrefix, 0, 1);
            static::setFinalValue($params[$currentPrefix], $explodedPrefix, $finalValue);
        }

    }

}