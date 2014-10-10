<?php


namespace Archiweb\Error;


class ErrorManager {

    /**
     * @var array
     */
    static protected $definedErrors;

    /**
     * @var array
     */
    protected $errors = array();

    /**
     * @var string
     */
    protected $lang;

    /**
     * @param string $lang
     */
    public function __construct ($lang) {

        $this->lang = $lang;

    }

    /**
     * @param Error $error
     */
    static public function addDefinedError (Error $error) {

        self::$definedErrors[] = $error;

    }

    /**
     * @return array
     */
    static public function getDefinedErrors () {

        return self::$definedErrors;

    }

    /**
     * @param Error  $error
     * @param string $field
     */
    public function addError (Error $error, $field = NULL) {

        $this->errors[] = $error;

    }

    /**
     * @return array
     */
    public function getErrors () {

        return $this->errors;

    }

    /**
     * @param Error $error
     */
    public function getFormattedError (Error $error = NULL) {

    }

}