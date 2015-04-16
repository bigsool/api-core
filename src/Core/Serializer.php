<?php

namespace Core;

use Core\Context\ActionContext;
use Core\Util\ArrayExtra;

class Serializer {

    /**
     * @var ActionContext
     */
    protected $actCtx;

    /**
     * @var bool
     */
    private $inProxyMode = false;

    /**
     * @var array
     */
    private $requiredKeyPaths = [];

    /**
     * @var array
     */
    private $dataSerialized;

    /**
     * @param ActionContext $actCtx
     */
    function __construct (ActionContext $actCtx) {

        $this->actCtx = $actCtx;

        $returnedKeyPaths = $actCtx->getRequestContext()->getReturnedFields();

        foreach ($returnedKeyPaths as $keyPath) {
            $this->requiredKeyPaths[] = explode('.', $keyPath->getValue());
        }

    }

    /**
     * @return boolean
     */
    public function isInProxyMode () {

        return $this->inProxyMode;
    }

    /**
     * @param boolean $inProxyMode
     */
    public function setInProxyMode ($inProxyMode) {

        $this->inProxyMode = !!$inProxyMode;
    }

    /**
     * @param mixed $data
     *
     * @return Serializer
     */
    public function serialize ($data) {

        if (is_array($data)) {
            $this->convertDateTime($data);
        }

        if (is_array($data) && !$this->inProxyMode) {
            $this->dataSerialized = $this->removeDoctrineId($this->requiredKeyPaths, $data);
            $this->dataSerialized = $this->keepOnlyRequestedFields($this->dataSerialized);
        }
        else {
            $this->dataSerialized = $data;
        }

        $this->dataSerialized = ['success' => true, 'data' => $this->dataSerialized];

        return $this;

    }

    public function convertDateTime (array &$data) {

        array_walk_recursive($data, function (&$value) {

            if ($value instanceof \DateTime) {
                $value = $value->format($value::ISO8601);
            }

        });

    }

    /**
     * @param [] String $keyPaths
     * @param [] String $data
     *
     * @return Array
     */
    private function removeDoctrineId ($keyPaths, $data) {

        $newData = [];

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if ($key === "id" && $this->isAutomaticallyAdded($keyPaths)) {
                    continue;
                }
                $newData[$key] = $this->removeDoctrineId($this->getKeyPaths($keyPaths, $key), $value);
            }
        }
        else {
            $newData = $data;
        }

        return $newData;

    }

    /**
     * @param [] String $keyPaths
     *
     * @return boolean
     */
    protected function isAutomaticallyAdded ($keyPaths) {

        foreach ($keyPaths as $keyPath) {
            if ($keyPath[0] === 'id') {
                return false;
            }
        }

        return true;

    }

    /**
     * @param [] String $keyPaths
     * @param mixed $value
     *
     * @return Array
     */
    protected function getKeyPaths ($keyPaths, $value) {

        $newKeyPaths = [];

        foreach ($keyPaths as $keyPath) {

            if ($keyPath[0] === $value) {

                $newKeyPath = [];

                for ($i = 1; $i < count($keyPath); ++$i) {
                    $newKeyPath[] = $keyPath[$i];
                }

                if (count($newKeyPath) != 0) {
                    $newKeyPaths[] = $newKeyPath;
                }

            }

        }

        return $newKeyPaths ? $newKeyPaths : $keyPaths;

    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function keepOnlyRequestedFields ($data) {

        $isAssociative = ArrayExtra::isAssociative($data);
        if ($isAssociative) {
            $data = [$data];
        }

        $newData = [];
        foreach ($data as $currData) {
            if (!is_array($currData)) {
                $newData[] = $currData;
                continue;
            }
            $currNewData = [];
            foreach ($this->actCtx->getRequestContext()->getReturnedFields() as $field) {
                ArrayExtra::magicalSet($currNewData, $field->getValue(),
                                       ArrayExtra::magicalGet($currData, $field->getValue()));
            }
            $newData[] = $currNewData;
        }

        return $isAssociative ? $newData[0] : $newData;

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