<?php


namespace Core\Error;


class LocalizedError extends Error {
    /**
     * @var string
     */
    protected $frMessage;

    /**
     * @var string
     */
    protected $enMessage;

    /**
     * @param int    $code
     * @param string $frMessage
     * @param string $enMessage
     * @param int    $parentCode
     * @param string $field
     */
    public function __construct ($code, $frMessage, $enMessage, $parentCode = NULL, $field = NULL) {

        parent::__construct($code, $enMessage?: $frMessage, $parentCode, $field);

        $this->frMessage = $frMessage;
        $this->enMessage = $enMessage;

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

}