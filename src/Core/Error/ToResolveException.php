<?php

namespace Core\Error;

class ToResolveException extends \Exception {

    /**
     * @var int
     */
    protected $errorCode;

    /**
     * @var string
     */
    protected $field;

    /**
     * @param string $errorCode
     * @param null   $field
     */
    public function __construct ($errorCode, $field = NULL) {

        $this->errorCode = $errorCode;
        $this->field = $field;

    }

    /**
     * @return int
     */
    public function getErrorCode () {

        return $this->errorCode;

    }

    /**
     * @return string
     */
    public function getField () {

        return $this->field;

    }

}