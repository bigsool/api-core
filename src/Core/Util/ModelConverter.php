<?php


namespace Core\Util;


use Core\Context\ApplicationContext;
use Core\Field\ResolvableField;

class ModelConverter {

    /**
     * @var ApplicationContext
     */
    protected $applicationContext;

    /**
     * @param ApplicationContext $appCtx
     */
    public function __construct (ApplicationContext $appCtx) {

        $this->applicationContext = $appCtx;

    }

    /**
     * @param mixed    $object
     * @param string[] $requestedFields
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
     * @param string[] $requestedFields
     * @param array    $formattedFields
     */
    protected function formatFields (array $requestedFields, array &$formattedFields) {

        foreach ($requestedFields as $requestedField) {
            if ($requestedField instanceof ResolvableField) {
                $requestedField = $requestedField->getAlias();
            }
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

        if (!is_object($object)) {
            throw new \RuntimeException('Unexpected type of $object');
        }

        $class = get_class($object);
        $entity = ($pos = strrpos($class, '\\')) ? substr($class, $pos + 1) : $class;
        $metadata = $this->applicationContext->getClassMetadata($class);

        $fieldNames =
            array_merge($metadata->getFieldNames(),
                        array_keys($this->applicationContext->getCalculatedFields($entity)));
        $associationNames = $metadata->getAssociationNames();

        foreach ($requestedFields as $requestedFieldName => $childRequestedField) {

            if (!is_array($childRequestedField)) {
                $requestedFieldName = $childRequestedField;
            }

            if ($requestedFieldName == '*') {
                $requestedFieldNames = $fieldNames;
            }
            else {
                $requestedFieldNames = [$requestedFieldName];
            }

            foreach ($requestedFieldNames as $requestedFieldName) {

                $method = 'get' . ucfirst($requestedFieldName);

                if (!is_callable([$object, $method])) {
                    continue;
                }

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

}