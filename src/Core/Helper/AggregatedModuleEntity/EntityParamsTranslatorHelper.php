<?php


namespace Core\Helper\AggregatedModuleEntity;


use Core\Module\ModelAspect;
use Core\Util\ArrayExtra;

class EntityParamsTranslatorHelper {

    /**
     * converts ['firm' => []] to ['company' => []] and ['student_name' => 'qwe'] to ['student']['name'] = 'qwe'
     *
     * @param array         $params
     * @param ModelAspect[] $modelAspects
     *
     * @return array
     */


    public static function translatePrefixesToKeyPaths ($params, $modelAspects) {

        $formattedParams = [];

        foreach ($modelAspects as $modelAspect) {

            $explodedPrefix = [];

            if ($modelAspect->getPrefix()) {
                $explodedPrefix = explode('.', $modelAspect->getPrefix());
                $data = $params;
                foreach ($explodedPrefix as $elem) {
                    if (is_object($data) || !isset($data[$elem])) {
                        continue 2;
                    }
                    $data = $data[$elem];
                }
            }
            else {
                $data = $params;
            }

            if ($modelAspect->getRelativeField()) {
                $explodedKeyPath = explode('.', $modelAspect->getRelativeField());
                $data = Helper::buildArrayWithKeys($explodedKeyPath, $data);
            }

            $formattedParams = ArrayExtra::array_merge_recursive_distinct($formattedParams, $data);

            if ($modelAspect->getPrefix() && $modelAspect->getPrefix() != $modelAspect->getRelativeField()) {
                $formattedParams = Helper::removeKeysFromArray($explodedPrefix, $formattedParams);
            }

        }

        $formattedParams = static::handlePrefixedFields($formattedParams, $modelAspects);

        return $formattedParams;

    }

    /**
     * @param array         $params
     * @param ModelAspect[] $modelAspects
     *
     * @return array
     */
    protected static function handlePrefixedFields ($params, $modelAspects) {

        foreach ($modelAspects as $modelAspect) {

            $data = $params;
            $explodedRelativeField = [];

            if ($modelAspect->getRelativeField()) {
                $explodedRelativeField = explode('.', $modelAspect->getRelativeField());
            }

            foreach ($explodedRelativeField as $elem) {
                if (is_object($data) | !isset($data[$elem])) {
                    $data = [];
                    break;
                }
                $data = $data[$elem];
            }

            if ($data) {
                $params = static::formatPrefixedFieldsToArray($params, $data, $modelAspects);
            }

        }

        return $params;

    }

    /**
     * WARNING : this code is almost the same as AggregatedModuleEntitySerializerContext::transformPrefixedFields
     * TODO : thierry fix your shit
     *
     * @param array         $params
     * @param               $data
     * @param ModelAspect[] $modelAspects
     *
     * @return array
     */
    protected static function formatPrefixedFieldsToArray (array $params, $data, $modelAspects) {

        $keysToRemove = [];

        foreach ($data as $key => $value) {

            if (strpos($key, '_') === false || is_array($value)) {
                continue;
            }

            $field = $key;

            for ($i = 0; ; ++$i) {

                $explodedKey = explode('_', $field);
                $prefix = implode('_', array_slice($explodedKey, 0, $i + 1));
                $prefix = str_replace('_', '.', $prefix);

                if ($i + 1 == count($explodedKey) - 1) {
                    $relativeField = Helper::getRelativeFieldForPrefix($prefix, $modelAspects);
                    if ($relativeField) {
                        $explodedRelativeField = explode('.', $relativeField);
                        $explodedRelativeField[] = $explodedKey[count($explodedKey) - 1];
                        $data = Helper::buildArrayWithKeys($explodedRelativeField, $value);
                        $params = ArrayExtra::array_merge_recursive_distinct($params, $data);
                    }

                    break;

                }

            }

            $keysToRemove[] = $field;

        }

        $params = static::removePrefixedFields($params, $keysToRemove);

        return $params;

    }

    /**
     * @param mixed $params
     * @param array $keysToRemove
     *
     * @return mixed
     */
    protected function removePrefixedFields ($params, $keysToRemove) {

        if (is_array($params)) {
            foreach ($params as $key => $value) {
                if ($key && in_array($key, $keysToRemove)) {
                    unset($params[$key]);
                }
                else {
                    $params[$key] = static::removePrefixedFields($value, $keysToRemove);
                }
            }
        }

        return $params;

    }

}