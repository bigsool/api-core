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
     * @param string  $lang
     */
    public function __construct ($lang) {

        FormattedError::setLang($lang);

    }

    /**
     * @param Error $error
     */
    static public function addDefinedError (Error $error) {

        self::$definedErrors[] = $error;

    }

    /**
     * @param Error $error
     * @param string $field
     */
    public function addError (Error $error, $field = null) {
        $this->errors[] = $error;

    }

    /**
     * @return array
     */
    public function getErrors () {

        return $this->errors;

    }

    /**
     * @return array
     */
    static public function getDefinedErrors () {

        return self::$definedErrors;

    }

    /**
     * @param Error $error
     */
    public function getFormattedError (Error $error = null) {

        if ($error) {
            return new FormattedError($error);
        }

        // TOCONTINUE

    }



}