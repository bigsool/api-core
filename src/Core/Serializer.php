<?php

namespace Core;

use Core\Context\RequestContext;

class Serializer {

    /**
     * @var array
     */
    private $requiredFields = [];

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
            $this->requiredFields[] = ["keyPath" => explode('.', $keyPath->getValue()), "alias" => $keyPath->getAlias()];
        }

    }

    /**
     * @param mixed $data
     *
     * @return Serializer
     */
    public function serialize ($data) {

        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $this->dataSerialized[$key] = $this->getSerializedData($value);
            }
        }
        else {
            $this->dataSerialized = $data;
        }

        return $this;

    }

    private function getSerializedData ($data) {

        $dataSerialized = [];
        if (!$this->requiredFields) return $data;
        foreach ($this->requiredFields as $field) {
            $arraySerialized = $this->getSerializedArray($field['keyPath'],$data[$field['alias']]);
            $dataSerialized = array_merge_recursive($dataSerialized,$arraySerialized);
        }

        return $dataSerialized;

    }

    private function getSerializedArray ($values,$elem) {

        $tab[$values[count($values) -1]] = $elem;
        $tab2 = [];
        for ($i = count($values) -2 ; $i >= 0 ; --$i) {
            $tab2[$values[$i]] = $tab;
            $tab = $tab2;
            $tab2 = [];
        }

        return $tab;

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