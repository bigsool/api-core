<?php


namespace Core\Util;


use Core\Context\ApplicationContext;

class ModelConverter {

    /**
     * @param mixed $object
     * @param array $requestedFields
     *
     * @return array
     */
    public function toArray ($object, array $requestedFields) {

        $result = [];

        // format requested fields
        $fields = [];
        $this->formatFields($requestedFields, $fields);

        $this->_toArray($object, $fields, $result);

        return $result;

    }

    /**
     * @param array $requestedFields
     * @param array $formattedFields
     */
    protected function formatFields (array $requestedFields, array &$formattedFields) {

        foreach ($requestedFields as $requestedField) {
            $exploded = explode('.', $requestedField, 2);
            if (count($exploded) == 2) {
                if (!isset($formattedFields[$exploded[0]])) {
                    $formattedFields[$exploded[0]] = [];
                }
                $this->formatFields([$exploded[1]], $formattedFields[$exploded[0]]);
            }
            else {
                $formattedFields[] = $requestedField;
            }
        }
    }

    /**
     * @param mixed $object
     * @param array $requestedFields
     * @param array $result
     */
    protected function _toArray ($object, array $requestedFields, array &$result) {

        if (is_null($object)) {
            return;
        }

        $metadata = ApplicationContext::getInstance()->getClassMetadata(get_class($object));

        $fieldNames = $metadata->getFieldNames();
        $associationNames = $metadata->getAssociationNames();

        foreach ($requestedFields as $requestedFieldName => $childRequestedField) {

            if (!is_array($childRequestedField)) {
                $requestedFieldName = $childRequestedField;
            }

            $method = 'get' . ucfirst($requestedFieldName);
            $isAttribute = in_array($requestedFieldName, $fieldNames);
            $isAssociation = in_array($requestedFieldName, $associationNames);
            $isCollection = $isAssociation && $metadata->isCollectionValuedAssociation($requestedFieldName);

            if ($isAttribute) {
                $result[$requestedFieldName] = $object->$method();
                continue;
            }

            if (!$isCollection) {
                $result[$requestedFieldName] = [];
                $this->_toArray($object->$method(), $childRequestedField, $result[$requestedFieldName]);
                continue;
            }

            $result[$requestedFieldName] = [];
            $collection = $object->$method();
            foreach ($collection as $childObject) {
                $childResult = [];
                $result[$requestedFieldName][] = &$childResult;
                $this->_toArray($childObject, $childRequestedField, $childResult);
                unset($childResult); // explicit destroy otherwise $childResult is shared between each children
            }

        }

    }

}