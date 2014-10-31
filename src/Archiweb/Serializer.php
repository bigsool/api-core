<?php

namespace Archiweb;

use Archiweb\Context\RequestContext;

class Serializer {

    private $reqCtx;
    private $entitiesRequired = ['User' => ['email', 'name', 'password', 'company'], 'Company' => ['id', 'storage'], 'Storage' =>['url']];

    function __construct (RequestContext $reqCtx) {

        $this->reqCtx = $reqCtx;
        //$returnedKeyPaths = $reqCtx->getReturnedKeyPaths();

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

        $serializedResult = [];

        $entities = is_array($result) ? $result : array($result);

        $i = 0;

        foreach ($entities as $entity) {

            $serializedResult[$i] = [];

            $entityPath = get_class($entity);
            $entityPathExploded = explode('\\', $entityPath);
            $entityName = $entityPathExploded[count($entityPathExploded) - 1];

            $requiredFields = $this->entitiesRequired[$entityName];

            foreach ($requiredFields as $requiredField) {
                $getter = "get" . ucfirst($requiredField);
                if (method_exists($entity, $getter)) {
                    $serializedResult[$i][$requiredField] =
                        is_object($entity->$getter()) ? $this->getSerializedResult($entity->$getter())
                            : $entity->$getter();
                }
            }

            ++$i;

        }

        return $serializedResult;

    }

}