<?php


namespace Archiweb\Error;


class FormattedError {

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
     * @var array
     */
    protected $childErrors;

    /**
     * @param Error $error
     */
    public function __construct ($error) {
    }

}