<?php

namespace Core;

use Core\Context\RequestContext;

class Serializer {

    /**
     * @var array
     */
    private $requiredKeyPaths = [];

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
            $this->requiredKeyPaths[] = explode('.', $keyPath->getValue());
        }

    }

    /**
     * @param mixed $data
     *
     * @return Serializer
     */
    public function serialize ($data) {

        if (is_array($data)) {
               $this->dataSerialized = $this->removeDoctrineId($this->requiredKeyPaths,$data);
        }
        else {
            $this->dataSerialized = $data;
        }

        return $this;

    }

    /**
     * @param [] String $keyPaths
     * @param [] String $data
     * @return Array
     */
    private function removeDoctrineId ($keyPaths,$data) {

        $newData = [];

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($key === "id" && $this->isAutomaticallyAdded($keyPaths)) {
                    continue;
                }
                $newData[$key] = $this->removeDoctrineId($this->getKeyPaths($keyPaths,$key),$value);
            }
        }
        else {
            $newData = $data;
        }

        return $newData;

    }

    /**
     * @param [] String $keyPaths
     * @param mixed $value
     * @return Array
     */
    protected function getKeyPaths($keyPaths,$value) {

        $newKeyPaths = [];

        foreach ($keyPaths as $keyPath) {

            if ($keyPath[0] === $value) {

                $newKeyPath = [];

                for ($i = 1 ; $i < count($keyPath) ; ++$i) {
                    $newKeyPath[] = $keyPath[$i];
                }

                if (count ($newKeyPath) != 0) {
                    $newKeyPaths[] = $newKeyPath;
                }

            }

        }

        return $newKeyPaths ? $newKeyPaths : $keyPaths;

    }

    /**
     * @param [] String $keyPaths
     * @return boolean
     */
    protected function isAutomaticallyAdded ($keyPaths) {

        foreach($keyPaths as $keyPath) {
            if ($keyPath[0] === 'id') return false;
        }

        return true;

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