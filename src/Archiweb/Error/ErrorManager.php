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
     * @return array
     */
    static public function getDefinedErrors () {

        return self::$definedErrors;

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
     * @param Error $error
     * @return Error
     */
    private function getMainParent ($error) {

        $parent = $error;
        foreach (self::$definedErrors as $definedError) {
            if ($definedError->getCode() == $error->getParentCode()) {
                $parent = $this->getMainParent($definedError);
                break;
            }
        }
        return $parent;
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

        return null;

    }

    /**
     * @param Error $parentCode
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
     * @param Error $childError
     * @param Error $error
     * @return boolean
     */
    private function parentOf ($childError, $error, &$isParent) {

        $parent = $this->getParent($childError);

        if ($parent != null) {
            if ($parent->getCode() == $error->getCode()) {
                $isParent = true;
            }
            else {
                $this->parentOf($parent,$error,$isParent);
            }
        }

        return $isParent;

    }

    /**
     * @param Error $errorToCheck
     * @return boolean
     */
    private function isInTheErrorThree ($errorToCheck) {

        if (in_array($errorToCheck,$this->errors)) return true;

        $isParent = false;

        foreach ($this->errors as $error) {

            $this->parentOf($error,$errorToCheck,$isParent);

        }

        return $isParent;

    }

    /**
     * @param Error $error
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
     * @param Error $error
     * @return FormattedError
     */
    public function getFormattedError (Error $error = null) {

        if ($error) {
            return new FormattedError($error);
        }
        $parent = $this->getMainParent($this->errors[1]);
        $formattedError = $this->buildFormattedError($parent);

        return $formattedError;

    }



}