<?php


namespace Archiweb\Error;


class ErrorManager {

    /**
     * @var Error[]
     */
    static protected $definedErrors;

    /**
     * @var Error[]
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

        $errorCode = $error->getCode();
        if (isset(self::$definedErrors[$errorCode])) {
            throw new \RuntimeException('already defined error ' . $errorCode);
        }

        self::$definedErrors[$errorCode] = $error;

    }

    /**
     * @param int    $errorCode
     * @param string $field
     */
    public function addError ($errorCode, $field = NULL) {

        if (!isset(self::$definedErrors[$errorCode])) {
            throw new \RuntimeException('undefined error code ' . $errorCode);
        }
        $error = self::$definedErrors[$errorCode];
        if (!in_array($error, $this->errors)) {
            $this->errors[] = $error;
        }

    }

    /**
     * @return Error[]
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