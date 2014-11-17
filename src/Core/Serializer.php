<?php

namespace Core;

use Core\Context\RequestContext;

class Serializer {

    /**
     * @var array
     */
    private $keyPathArrays = [];

    /**
     * @var array
     */
    private $dataSerialized;

    /**
     * @param RequestContext $reqCtx
     */
    function __construct (RequestContext $reqCtx) {

        $returnedKeyPaths = $reqCtx->getReturnedKeyPaths();

        foreach ($returnedKeyPaths as $keyPath) {
            $this->keyPathArrays[] = explode('.', $keyPath->getValue());
        }

    }

    /**
     * @param mixed $result
     *
     * @return Serializer
     */
    public function serialize ($data) {

        $dataToSerialized = [];
        $dataAlreadySerialized = [];

        if (is_array($data)) {

            foreach ($data as $value) {
                if (is_object($value)) {
                    $dataToSerialized[] = $value;
                }
                else {
                    foreach ($value as $elem) {
                        if (is_object($elem)) {
                            $dataToSerialized[] = $elem;
                        }
                    }
                }
            }

            $dataSerialized = $this->getSerializedData($dataToSerialized);
            $dataSerialized = array_merge($dataAlreadySerialized, $dataSerialized);

        }
        else {
            if (is_object($data)) {
                $dataSerialized = $this->getSerializedData($data);
            }
            else {
                $dataSerialized = (string)$data;
            }
        }

        $this->dataSerialized = $dataSerialized;

        return $this;

    }

    /**
     * @param mixed $data
     *
     * @return array
     */
    private function getSerializedData ($data) {

        $entities = is_array($data) ? $data : array($data);
        $resultSerialized = [];
        foreach ($entities as $entity) {
            $entitySerialized = [];
            foreach ($this->keyPathArrays as $keyPathArray) {
                $entitySerialized = array_merge($entitySerialized, $this->parseKeyPath($entity, $keyPathArray));
            }
            $resultSerialized[] = $entitySerialized;
        }

        return is_array($data) ? $resultSerialized : $resultSerialized[0];

    }

    /**
     * @param mixed $entity
     * @param array $keyPathArray
     *
     * @return array
     */
    private function parseKeyPath ($entity, $keyPathArray) {

        $result = [];

        $currentElemKeyPath = $keyPathArray[0];
        $getter = "get" . ucfirst($currentElemKeyPath);

        if (!method_exists($entity, $getter)) {
            return $result;
        }

        $object = $entity->$getter();

        if ($object instanceof \Traversable) {
            array_shift($keyPathArray);
            foreach ($object as $elem) {
                $entityName = $this->getEntityNameFromKeyPath($elem);
                $result[$entityName][] = $this->parseKeyPath($elem, $keyPathArray);
            }
        }
        else {
            if (is_object($object)) {
                $entityName = $this->getEntityNameFromKeyPath($object);
                array_shift($keyPathArray);
                $result[$entityName] = $this->parseKeyPath($object, $keyPathArray);
            }
            else {
                $result[$currentElemKeyPath] = $object;
            }
        }

        return $result;

    }

    /**
     * @param KeyPath $keyPath
     */
    private function getEntityNameFromKeyPath ($keyPath) {

        $entityPath = get_class($keyPath);
        $entityPathExploded = explode('\\', $entityPath);

        return $entityPathExploded[count($entityPathExploded) - 1];

    }

    /**
     * @return string
     */
    public function getJSON () {

        return json_encode($this->dataSerialized);

    }

    /**
     * @return mixed
     */
    public function get () {

        return $this->dataSerialized;

    }

}