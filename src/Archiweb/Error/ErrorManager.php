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
     * @param string $lang
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

        $error = $this->getErrorForErrorCode($errorCode);
        if (!in_array($error, $this->errors)) {
            $this->errors[] = $error;
        }

    }

    protected function getErrorForErrorCode ($errorCode) {

        if (!isset(self::$definedErrors[$errorCode])) {
            throw new \RuntimeException('undefined error code ' . $errorCode);
        }

        return self::$definedErrors[$errorCode];

    }

    /**
     * @return Error[]
     */
    public function getErrors () {

        return $this->errors;

    }

    /**
     * @param int $errorCode
     *
     * @return FormattedError
     */
    public function getFormattedError ($errorCode = NULL) {

        if ($errorCode) {
            return new FormattedError($this->getErrorForErrorCode($errorCode));
        }
        $parent = $this->getMainParent($this->errors[0]);
        $formattedError = $this->buildFormattedError($parent);

        return $formattedError;

    }

    /**
     * @param Error $error
     *
     * @return Error
     */
    private function getMainParent ($error) {

        $parent = $error;
        if ($error->getParentCode() != NULL) {
            foreach (self::$definedErrors as $definedError) {
                if ($definedError->getCode() == $error->getParentCode()) {
                    $parent = $this->getMainParent($definedError);
                    break;
                }
            }
        }

        return $parent;
    }

    /**
     * @param Error $error
     *
     * @return FormattedError
     */
    private function buildFormattedError (Error $error) {

        $childErrors = $this->getChildErrors($error->getCode());
        $formattedError = new FormattedError($error);

        if ($childErrors) {
            foreach ($childErrors as $childError) {
                if ($this->isInTheErrorThree($childError)) {
                    $formattedError->addChildError($this->buildFormattedError($childError));
                }
            }
        }

        return $formattedError;

    }

    /**
     * @param Error $parentCode
     *
     * @return array
     */
    private function getChildErrors ($parentCode) {

        $childErrors = [];
        foreach (self::$definedErrors as $definedError) {
            if ($definedError->getParentCode() == $parentCode) {
                $childErrors[] = $definedError;
            }
        }

        return $childErrors;

    }

    /**
     * @param Error $errorToCheck
     *
     * @return boolean
     */
    private function isInTheErrorThree ($errorToCheck) {

        if (in_array($errorToCheck, $this->errors)) {
            return true;
        }

        $isParent = false;

        foreach ($this->errors as $error) {

            $this->parentOf($error, $errorToCheck, $isParent);

        }

        return $isParent;

    }

    /**
     * @param Error $childError
     * @param Error $error
     *
     * @return boolean
     */
    private function parentOf ($childError, $error, &$isParent) {

        $parent = $this->getParent($childError);

        if ($parent != NULL) {
            if ($parent->getCode() == $error->getCode()) {
                $isParent = true;
            }
            else {
                $this->parentOf($parent, $error, $isParent);
            }
        }

        return $isParent;

    }

    /**
     * @return Error
     */
    private function getParent ($error) {

        foreach (self::$definedErrors as $definedError) {
            if ($definedError->getCode() == $error->getParentCode()) {
                return $definedError;
            }
        }

        return NULL;

    }

}