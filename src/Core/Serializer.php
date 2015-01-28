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
     * @param mixed $data
     *
     * @return Serializer
     */
    public function serialize ($data) {

        $this->dataSerialized = $this->isDataObject($data) ? $this->serializeObject($data) : $this->serializeArray($data);

        return $this;

    }

    /**
     * @param mixed $data
     *
     * @return array
     */
    private function serializeObject ($data) {

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $dataSerialized[$key] = $this->getSerializedData($value);
            }
        }
        else {
            $dataSerialized =  $this->getSerializedData($data);
        }

        return $dataSerialized;

    }

    /**
     * @param mixed $data
     *
     * @return array
     */
    private function serializeArray ($data) {

        if (is_array($data) && $this->keyPathArrays) {
            if (array_key_exists(1,$data)) {
                foreach ($data as $key => $value) {
                    $dataSerialized[] = $this->getSerializedDataArray($this->keyPathArrays,0,$value[0]);
                }
            }
            else {
                $dataSerialized = $this->getSerializedDataArray($this->keyPathArrays,0,$data[0]);
            }
            return $dataSerialized;
        }
        else {
            return $data;
        }

    }


    private function getSerializedDataArray ($keyPathArrays,$level,$data) {

        $dataSerialized = [];

        foreach ($keyPathArrays as $keyPathArray) {

            if (is_array($data[$keyPathArray[$level]])) {

                $newDataSerialized = $this->getSerializedDataArray([$keyPathArray],$level + 1,$data[$keyPathArray[$level]]);

                if (array_key_exists($keyPathArray[$level],$dataSerialized)) {
                    $dataSerialized[$keyPathArray[$level]] = array_merge_recursive($dataSerialized[$keyPathArray[$level]],$newDataSerialized);
                }
                else {
                    $dataSerialized[$keyPathArray[$level]] = $newDataSerialized;
                }

            }
            else {

                $dataSerialized[$keyPathArray[$level]] = $data[$keyPathArray[$level]];

            }

        }

        return $dataSerialized;

    }


    /**
     * @param mixed $data
     *
     * @return array
     */
    private function getSerializedData ($data) {


        if(!$this->keyPathArrays) throw new \Exception('serialization impossible');

        $entitySerialized = [];

        foreach ($this->keyPathArrays as $keyPathArray) {
            $bla = $this->parseKeyPath($data, $keyPathArray);
            $entitySerialized = array_merge_recursive($entitySerialized, $bla);
        }

        return $entitySerialized;

    }


    private function isDataObject ($data) {

        return (is_object($data) || (is_array($data) && is_object($data[0])));

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
            array_shift($keyPathArray);
            if (is_object($object) && $keyPathArray) {
                $entityName = $this->getEntityNameFromKeyPath($object);
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

        return lcfirst($entityPathExploded[count($entityPathExploded) - 1]);

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