<?php


namespace Archiweb\Error;


class Error {

    /**
     * @var int
     */
    protected $code;

    /**
     * @var string
     */
    protected $frMessage;

    /**
     * @var string
     */
    protected $enMessage;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var int
     */
    protected $parentCode;

    /**
     * @param int    $code
     * @param string $frMessage
     * @param string $enMessage
     * @param string $field
     * @param int    $parentCode
     */
    public function __construct ($code, $frMessage, $enMessage, $field = NULL, $parentCode = NULL) {

        $this->code = $code;
        $this->frMessage = $frMessage;
        $this->enMessage = $enMessage;
        $this->field = $field;
        $this->parentCode = $parentCode;

    }

    /**
     * @return int
     */
    public function getCode () {

        return $this->code;

    }

    /**
     * @return string
     */
    public function getFrMessage () {

        return $this->frMessage;

    }

    /**
     * @return string
     */
    public function getEnMessage () {

        return $this->enMessage;

    }

    /**
     * @return string
     */
    public function getField () {

        return $this->field;

    }

    /**
     * @return array
     */
    public function getParentCode () {

        return $this->parentCode;

    }

}