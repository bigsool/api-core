<?php

namespace Archiweb;

use Archiweb\Context\FindQueryContext;
use Archiweb\Context\RequestContext;
use Archiweb\Field\KeyPath;

class Serializer {

    private $reqCtx;
    private $keyPathArrays = [];
    private $entitiesRequired = ['User' => ['email', 'name', 'password', 'company'], 'Company' => ['id', 'storage'], 'Storage' =>['url']];

    function __construct (RequestContext $reqCtx, $registry = null) {

        $this->reqCtx = $reqCtx;
        $returnedKeyPaths = $reqCtx->getReturnedKeyPaths();

     /*   foreach ($returnedKeyPaths as $keyPath) {
            $qryCtx = new FindQueryContext('User');
            $qryCtx->addKeyPath(new KeyPath('*'));
            $path = $keyPath->resolve($registry,$qryCtx);
            $this->keyPathArrays[] = explode('.',$path);
        }*/
        $keyPaths = ['email','company.name','company.storage.url'];
        foreach ($keyPaths as $keyPath) {
            $this->keyPathArrays[] = explode('.',$keyPath);
        }
    }

    public function serialize (array $result) {

        $dataToSerialized = [];
        $dataAlreadySerialized = [];

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
                       // $dataAlreadySerialized[] = $elem; Pas forcement le mettre
                    }
                }
            }
        }

        $dataSerialized = $this->getSerializedResult($dataToSerialized);
        $dataSerialized = array_merge($dataAlreadySerialized, $dataSerialized);

        return json_encode($dataSerialized);

    }

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

        return $resultSerialized;

    }

    private function parseKeyPath($entity,$keyPathArray) {

        $result = [];

        $currentElemKeyPath = $keyPathArray[0];
        $getter = "get" . ucfirst($currentElemKeyPath);
        if (!method_exists($entity, $getter)) return $result;

        $object = $entity->$getter();

        if (is_array($object)) {
            $entityName = $this->getEntityNameFromKeyPath($object[0]);
            $this->initNextArrayKeyPath($keyPathArray);
            foreach ($object as $elem) {
                $result[$entityName] = $this->parseKeyPath($object,$keyPathArray);
            }
        }
        else if (is_object($object)) {
            $entityName = $this->getEntityNameFromKeyPath($object);
            $this->initNextArrayKeyPath($keyPathArray);
            $result[$entityName] = $this->parseKeyPath($object,$keyPathArray);
        }
        else {
            $result[$currentElemKeyPath] = $object;
        }

        return $result;

    }

    private function initNextArrayKeyPath (&$arrayKeyPath) {

        array_shift($arrayKeyPath);

    }

    private function getEntityNameFromKeyPath ($keyPath) {

        $entityPath = get_class($keyPath);
        $entityPathExploded = explode('\\', $entityPath);
        return $entityPathExploded[count($entityPathExploded) - 1];

    }


}