<?php


namespace Core\Context;


use Core\Field\RelativeField;
use Core\Helper\AggregatedModuleEntityHelper;
use Core\Module\AggregatedModuleEntityDefinition;
use Core\Module\ModelAspect;
use Core\Util\ArrayExtra;

class AggregatedSerializerContext {

    /**
     * @var AggregatedModuleEntityDefinition
     */
    protected $definition;

    /**
     * @param AggregatedModuleEntityDefinition $definition
     */
    public function __construct(AggregatedModuleEntityDefinition $definition) {

        $this->definition = $definition;

    }

    /**
     * @param RelativeField[] $requestedFields
     *
     * @return array
     */
    public function convertRequestedFields (array $requestedFields) {

        $reqCtxFormattedReturnedFields = [];
        $returnedFields = [];

        foreach ($requestedFields as $returnedField) {
            $returnedFields[] = $returnedField->getValue();
        }

        $returnedFields = $this->formatFindValues($returnedFields);

        foreach ($returnedFields as $returnedField) {
            $reqCtxFormattedReturnedFields[] = new RelativeField($returnedField);
        }

        return $reqCtxFormattedReturnedFields;
    }


    /**
     * Converts entity result array keys to translated keys
     * ie: company is translated back to firm and student is mapped to student_*
     * @param array $result
     *
     * @return array
     */

    public function formatResultArray ($result) {

        $resultFormatted = [];

        if ($result) {
            foreach ($result as $elem) {
                $elem = $this->formatResultWithPrefixedFields($elem);
                $resultFormatted[] = $this->formatArrayWithPrefix($elem);
            }
        }

        return $resultFormatted;

    }

    /**
     * @return \Core\Module\ModelAspect[]
     */
    protected function getModelAspects() {

        return $this->definition->getModelAspects();

    }

    /**
     * @return \Core\Module\ModelAspect[]
     */
    protected function getAllModelAspects() {

        return array_merge([$this->definition->getMainAspect()], $this->getModelAspects());

    }

    /**
     * @param mixed $result
     *
     * @return array
     */
    protected function formatResultWithPrefixedFields ($result) {

        $newResult = $result;

        /**
         * @var ModelAspect[] $modelAspectsWithPrefix
         */
        $modelAspectsWithPrefix = array_filter($this->getModelAspects(), function (ModelAspect $modelAspect) {

            return $modelAspect->isWithPrefixedFields();

        });

        foreach ($modelAspectsWithPrefix as $modelAspect) {

            $relativeField = $modelAspect->getRelativeField();

            $explodedRelativeField = explode('.', $relativeField);

            $data = $newResult;
            foreach ($explodedRelativeField as $elem) {
                if (!isset($data[$elem])) {
                    $data = [];
                    break;
                }
                $data = $data[$elem];
            }

            for ($i = count($explodedRelativeField) - 1; $i >= 0; --$i) {

                $currentRelativeField = implode('.', array_slice($explodedRelativeField, 0, $i));

                $currentModelAspect = $this->getModelAspectByRelativeField($currentRelativeField);

                if (!$currentRelativeField || ($currentModelAspect && !$currentModelAspect->isWithPrefixedFields())) {

                    foreach ($data as $key => $value) {

                        $oneModelAspect =                            $this->getModelAspectByRelativeField($relativeField . '.' . $key);

                        if (!$oneModelAspect || !$oneModelAspect->isWithPrefixedFields()) {

                            $currentExplodedPrefix =
                                $currentRelativeField ? explode('.', $currentModelAspect->getPrefix()) : [];
                            $explodedPrefix = explode('.', $modelAspect->getPrefix());
                            $explodedDiffPrefix = array_diff($explodedPrefix, $currentExplodedPrefix);

                            $prefixedKey = $key;
                            if ($oneModelAspect) {
                                $explodedPrefix = explode('.', $oneModelAspect->getPrefix());
                                $prefixedKey = $explodedPrefix[count($explodedPrefix) - 1];
                            }

                            $data[str_replace('.', '_', implode('.', $explodedDiffPrefix)) . '_' . $prefixedKey] =
                                $value;

                        }

                        unset($data[$key]);

                    }

                    if ($currentRelativeField) {
                        $data =
                            AggregatedModuleEntityHelper::buildArrayWithKeys(explode('.', $currentRelativeField),
                                                                             $data);
                    }

                    break;

                }

            }

            $newResult = ArrayExtra::array_merge_recursive_distinct($newResult, $data);

        }

        foreach ($modelAspectsWithPrefix as $modelAspect) {

            $newResult =
                AggregatedModuleEntityHelper::removeKeysFromArray(explode('.', $modelAspect->getRelativeField()),
                                                                  $newResult);

        }

        return $newResult;

    }

    /**
     * @param array $result
     *
     * @return array
     */
    protected function formatArrayWithPrefix ($result) {

        $formattedResult = [];

        foreach ($this->getAllModelAspects() as $modelAspect) {

            $relativeField = $modelAspect->getRelativeField();
            $prefix = $modelAspect->getPrefix();

            $explodedRelativeField = [];

            if ($relativeField) {
                $explodedRelativeField = explode('.', $relativeField);
                $data = $result;
                foreach ($explodedRelativeField as $elem) {
                    if (!isset($data[$elem])) {
                        continue 2;
                    }
                    $data = $data[$elem];
                }
            }
            else {
                $data = $result;
            }

            if ($modelAspect->getPrefix()) {
                $explodedPrefix = explode('.', $modelAspect->getPrefix());
                $data = AggregatedModuleEntityHelper::buildArrayWithKeys($explodedPrefix, $data);
            }

            $formattedResult = ArrayExtra::array_merge_recursive_distinct($formattedResult, $data);

            if ($relativeField && $relativeField != $prefix) {
                $formattedResult =
                    AggregatedModuleEntityHelper::removeKeysFromArray($explodedRelativeField, $formattedResult);
            }

        }

        return $formattedResult;

    }


    /**
     * @param string $relativeField
     *
     * @return mixed
     */
    protected function getModelAspectByRelativeField ($relativeField) {

        foreach ($this->getAllModelAspects() as $modelAspect) {

            if ($modelAspect->getRelativeField() && $modelAspect->getRelativeField() == $relativeField) {
                return $modelAspect;
            }

        }

        return NULL;

    }

    /**
     * @param Array $values
     *
     * @return Array
     */
    protected function formatFindValues ($values) {

        $formattedValues = [];

        foreach ($values as $value) {

            $explodedValue = explode('.', $value);
            $field = $explodedValue[count($explodedValue) - 1];
            $prefixed = false;

            if (count($explodedValue) > 1) {
                array_splice($explodedValue, count($explodedValue) - 1, 1);
                $value = implode('.', $explodedValue);
                foreach ($this->getAllModelAspects() as $modelAspect) {
                    if ($modelAspect->getPrefix() == $value) {
                        $value = $modelAspect->getRelativeField() . '.' . $field;
                        $prefixed = true;
                        break;
                    }
                }
            }

            if (!$prefixed) {
                $value = $field;
            }

            $formattedValues[] = $value;

        }

        $formattedValues = $this->transformPrefixedFields($formattedValues);

        return $formattedValues;

    }


    /**
     * @param array $fields
     *
     * @return array
     */
    // WARNING : this code is almost the same as AggregatedParamsTranslatorHelper::formatPrefixedFieldsToArray
    // TODO : thierry fix your shit
    private function transformPrefixedFields (array $fields) {

        $newParams = [];
        $fields = is_array($fields) ? $fields : [$fields];

        foreach ($fields as $key) {

            if (strpos($key, '_') === false) {
                $newParams[] = $key;
                continue;
            }

            $field = $key;

            for ($i = 0; ; ++$i) {

                $explodedKey = explode('_', $field);
                $prefix = implode('_', array_slice($explodedKey, 0, $i + 1));
                $prefix = str_replace('_', '.', $prefix);

                if ($i + 1 == count($explodedKey) - 1) {
                    $relativeField = AggregatedModuleEntityHelper::getRelativeFieldForPrefix($prefix, $this->getAllModelAspects());
                    if ($relativeField) {
                        $explodedRelativeField = explode('.', $relativeField);
                        $explodedRelativeField[] = $explodedKey[count($explodedKey) - 1];
                        $newParams[] = implode('.', $explodedRelativeField);
                    }

                    break;

                }

            }

        }

        return $newParams;

    }



}