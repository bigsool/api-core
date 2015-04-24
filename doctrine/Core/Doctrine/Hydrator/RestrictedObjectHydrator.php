<?php


namespace Core\Doctrine\Hydrator;


use Core\Util\ArrayExtra;
use Doctrine\ORM\Internal\Hydration\ObjectHydrator;

class RestrictedObjectHydrator extends ObjectHydrator {

    /**
     * {@inheritdoc}
     */
    protected function hydrateRowData (array $row, array &$result) {

        parent::hydrateRowData($row, $result);

        $hydrator = $this->_em->newHydrator('ArrayIdHydrator');
        $hydrator->_rsm = $this->_rsm;
        $hydrator->_hints = $this->_hints;
        $hydrator->prepare();
        $arrayResult = [];
        $hydrator->hydrateRowData($row, $arrayResult);

        //TODO: handle aggregated fields
        $this->setRestrictedIds($result, $arrayResult);

    }

    /**
     * @param array $_objectResult
     * @param array $_arrayResult
     */
    protected function setRestrictedIds (array &$_objectResult, array &$_arrayResult) {

        // https://doctrine-orm.readthedocs.org/en/latest/reference/dql-doctrine-query-language.html#pure-and-mixed-results
        $isPureResult = is_object($_objectResult[0]);

        $isAssociation = ArrayExtra::isAssociative($_arrayResult);

        if ($isAssociation) {
            $arrayResult = [$_arrayResult];
            $objectResult = [$_objectResult];
        }
        else {
            $arrayResult = &$_arrayResult;
            $objectResult = &$_objectResult;
        }

        foreach ($arrayResult as $array) {
            if (!$isPureResult) {
                $array = &$array[0];
            }
            if (!is_array($array)) {
                throw new \RuntimeException('$currentArrayResult must be an array');
            }
            if (!isset($array['id'])) {
                throw new \RuntimeException('id not found in $currentArrayResult');
            }
            $id = $array['id'];
            $object = NULL;
            foreach ($objectResult as $_object) {
                if (!$isPureResult) {
                    $_object = $_object[0];
                }
                if (is_callable([$_object, 'getId']) && $_object->getId() == $id) {
                    $object = $_object;
                    break;
                }
            }
            if (is_null($object)) {
                throw new \RuntimeException('$object not found');
            }
            $metadata = $this->_em->getClassMetadata(get_class($object));
            // foreach relations
            foreach ($array as $key => $values) {
                // id isn't a relation
                if ($key == 'id') {
                    continue;
                }
                $isCollection = $metadata->isCollectionValuedAssociation($key);
                if (count($values) && $isCollection == ArrayExtra::isAssociative($values)) {
                    throw new \RuntimeException('Unexpected structure for $values');
                }
                if (!$isCollection) {
                    $values = [$values];
                }
                $ids = [];
                foreach ($values as $value) {
                    if (!is_array($value)) {
                        throw new \RuntimeException('$value must be an array');
                    }
                    if (!isset($value['id'])) {
                        throw new \RuntimeException('id not found in $value');
                    }
                    $ids[] = $value['id'];
                }
                $propertyName = $key . 'RestrictedId' . ($isCollection ? 's' : '');
                // TODO: $refProp mustn't be instantiated more than once
                $refProp = new \ReflectionProperty($object, $propertyName);
                $refProp->setAccessible(true);
                if ($isCollection) {
                    $refProp->setValue($object, array_merge($refProp->getValue($object), $ids));
                }
                else {
                    $refProp->setValue($object, $ids[0]);
                }

                $newObjectResult = $metadata->getReflectionProperty($key)->getValue($object);
                if (!$isCollection) {
                    $newObjectResult = [$newObjectResult];
                }
                else {
                    // TODO: $refCollection mustn't be instantiated more than once
                    $refCollection = new \ReflectionProperty($newObjectResult, 'collection');
                    $refCollection->setAccessible(true);
                    $newObjectResult = $refCollection->getValue($newObjectResult)->toArray();
                }
                $newArrayResult = $values;
                if (count($newObjectResult)) {
                    $this->setRestrictedIds($newObjectResult, $newArrayResult);
                }
            }
        }
    }

}