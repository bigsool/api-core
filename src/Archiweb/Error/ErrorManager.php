<?php


namespace Archiweb\Error;


class ErrorManager {

    /**
     * @var Error[]
     */
    protected $definedErrors;

    /**
     * @var Error[]
     */
    protected $errors = array();

    /**
     * @var Error[]
     */
    protected $fields = [];

    /**
     * The constructor should be called only by the ApplicationContext
     */
    protected function __construct () {
    }

    /**
     * @param Error $error
     */
    public function defineError (Error $error) {

        $errorCode = $error->getCode();
        if (isset($this->definedErrors[$errorCode])) {
            throw new \RuntimeException('already defined error ' . $errorCode);
        }

        $this->definedErrors[$errorCode] = $error;

    }

    /**
     * @param string $errorCode
     *
     * @return Error
     */
    public function getDefinedError ($errorCode) {

        if (isset($this->definedErrors[$errorCode])) {
            return $this->definedErrors[$errorCode];
        }

        return NULL;

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
            $this->addError($errorCode);
        }

        $parent = $this->getMainParent($this->errors[0]);
        $formattedError = $this->buildFormattedError($parent);

        return $formattedError;

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
        if ($field) {
            $this->fields[$error->getCode()] = $field;
        }

    }

    /**
     * @param string $errorCode
     *
     * @return Error
     */
    protected function getErrorForErrorCode ($errorCode) {

        if (!isset($this->definedErrors[$errorCode])) {
            throw new \RuntimeException('undefined error code ' . $errorCode);
        }

        return $this->definedErrors[$errorCode];

    }

    /**
     * @param Error $error
     *
     * @return Error
     */
    private function getMainParent ($error) {

        $parent = $error;
        if ($error->getParentCode() !== NULL) {
            foreach ($this->definedErrors as $definedError) {
                if ($definedError->getCode() === $error->getParentCode()) {
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
        $field = isset($this->fields[$error->getCode()]) ? $this->fields[$error->getCode()] : NULL;
        $formattedError = new FormattedError($error, $field);

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
        foreach ($this->definedErrors as $definedError) {
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
     * @param Error $error
     *
     * @return Error|null
     */
    private function getParent (Error $error) {

        foreach ($this->definedErrors as $definedError) {
            if ($definedError->getCode() === $error->getParentCode()) {
                return $definedError;
            }
        }

        return NULL;

    }

}