<?php

namespace Archiweb;

use Archiweb\Context\RequestContext;

class Serializer {

    /**
     * @var RequestContext
     */
    private $reqCtx;

    /**
     * @var array
     */
    private $keyPathArrays = [];

    /**
     * @param RequestContext $reqCtx
     */
    function __construct (RequestContext $reqCtx) {

        $returnedKeyPaths = $reqCtx->getReturnedKeyPaths();

        foreach ($returnedKeyPaths as $keyPath) {
            $this->keyPathArrays[] = explode('.',$keyPath->getValue());
        }

    }

    /**
     * @param mixed $result
     * @return array
     */
    public function serialize ($result) {

        $dataToSerialized = [];
        $dataAlreadySerialized = [];

        if (is_array($result)) {

            foreach($result as $value) {
                if (is_object($value)) {
                    $dataToSerialized[] = $value;
                }
                else {
                    foreach ($value as $elem) {
                        if (is_object($elem)) {
                            $dataToSerialized[] = $elem;
                        }
                        else {
                            // $dataAlreadySerialized[] = $elem; We don't need that for the moment
                        }
                    }
                }
            }

            $dataSerialized = $this->getSerializedResult($dataToSerialized);
            $dataSerialized = array_merge($dataAlreadySerialized, $dataSerialized);

        }
        else {
            $dataSerialized = $this->getSerializedResult($result);
        }

        return json_encode($dataSerialized);

    }

    /**
     * @param mixed $result
     * @return array
     */
    private function getSerializedResult ($result) {

        $entities = is_array($result) ? $result : array($result);
        $resultSerialized = [];
        foreach ($entities as $entity) {
            $entitySerialized = [];
            foreach ($this->keyPathArrays as $keyPathArray) {
                $entitySerialized = array_merge($entitySerialized,$this->parseKeyPath($entity,$keyPathArray));
            }
            $resultSerialized[] = $entitySerialized;
        }

        return is_array($result) ? $resultSerialized : $resultSerialized[0];

    }

    /**
     * @param mixed $result
     * @return array
     */
    private function parseKeyPath($entity,$keyPathArray) {

        $result = [];

        $currentElemKeyPath = $keyPathArray[0];
        $getter = "get" . ucfirst($currentElemKeyPath);

        if (!method_exists($entity, $getter)) return $result;

        $object = $entity->$getter();

        if (is_array($object)) {
            $entityName = $this->getEntityNameFromKeyPath($object[0]);
            array_shift($keyPathArray);
            foreach ($object as $elem) {
                $result[$entityName][] = $this->parseKeyPath($elem,$keyPathArray);
            }
        }
        else if (is_object($object)) {
            $entityName = $this->getEntityNameFromKeyPath($object);
            array_shift($keyPathArray);
            $result[$entityName] = $this->parseKeyPath($object,$keyPathArray);
        }
        else {
            $result[$currentElemKeyPath] = $object;
        }

        return $result;

    }

    /**
     * @param mixed $arrayKeyPath
     *
     */
    private function getEntityNameFromKeyPath ($keyPath) {

        $entityPath = get_class($keyPath);
        $entityPathExploded = explode('\\', $entityPath);
        return $entityPathExploded[count($entityPathExploded) - 1];

    }


}