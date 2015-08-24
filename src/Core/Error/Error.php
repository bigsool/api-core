<?php


namespace Core\Error;


class Error {

    /**
     * @var int
     */
    protected $code;

    /**
     * @var string
     */
    protected $message;

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
     * @param string $message
     * @param int    $parentCode
     * @param string $field
     */
    public function __construct ($code, $message, $parentCode = NULL, $field = NULL) {

        $this->code = $code;
        $this->message = $message;
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
    public function getMessage () {

        return $this->message;

    }

    /**
     * @return string
     */
    public function getField () {

        return $this->field;

    }

    /**
     * @param string $field
     */
    public function setField ($field) {

        $this->field = strval($field);
    }

    /**
     * @return int
     */
    public function getParentCode () {

        return $this->parentCode;

    }

}