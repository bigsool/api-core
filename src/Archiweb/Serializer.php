<?php

namespace Archiweb;

class Serializer {

    private $entitiesRequired;

    function __construct ($entitiesRequired) {

        $this->entitiesRequired = $entitiesRequired;

    }

    public function serialize ($data) {

        return json_encode($this->getSerializedResult($data));

    }

    private function getSerializedResult ($entities) {

        $result = [];

        $entities = is_array($entities) ? $entities : array($entities);

        $i = 0;

        foreach ($entities as $entity) {

            $result[$i] = [];

            $entityPath = get_class($entity);
            $entityPathExploded = explode('\\', $entityPath);
            $entityName = $entityPathExploded[count($entityPathExploded) - 1];

            $requiredFields = $this->entitiesRequired[$entityName];

            foreach ($requiredFields as $requiredField) {
                $getter = "get" . ucfirst($requiredField);
                if (method_exists($entity, $getter)) {
                    $result[$i][$requiredField] =
                        is_object($entity->$getter()) ? $this->getSerializedResult($entity->$getter())
                            : $entity->$getter();
                }
            }

            ++$i;

        }

        return $result;

    }

}