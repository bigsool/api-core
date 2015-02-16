<?php


namespace Core\Error;


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
     * @param int $errorCode
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

        if ($errorCode === NULL) {
            $errors = $this->errors;
        }
        else {
            $errors = [$this->getErrorForErrorCode($errorCode)];
        }
        $formattedErrors = [];
        $parentCode = $errors[0]->getParentCode();
        foreach ($errors as $error) {
            if ($error->getParentCode() !== $parentCode) {
                throw new \RuntimeException("Errors to throw haven't the same parent");
            }
            $formattedErrors[] = $this->buildFormattedError($error);
        }

        $error = $errors[0];
        $formattedError = $formattedErrors[0];

        while ($error->getParentCode() !== NULL) {
            $parent = $this->getErrorForErrorCode($error->getParentCode());
            $parentFormattedError = $this->buildFormattedError($parent);
            if ($formattedError === $formattedErrors[0]) {
                foreach ($formattedErrors as $formattedError) {
                    $parentFormattedError->addChildError($formattedError);
                }
            }
            else {
                $parentFormattedError->addChildError($formattedError);
            }
            $formattedError = $parentFormattedError;
            $error = $parent;
        }

        return $formattedError;

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
     * @return FormattedError
     */
    private function buildFormattedError (Error $error) {

        $field = isset($this->fields[$error->getCode()]) ? $this->fields[$error->getCode()] : NULL;
        $formattedError = new FormattedError($error, $field);

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
     * @param Error $error
     *
     * @return Error
     */
    private function getMainParent ($error) {

        while ($error->getParentCode() !== NULL) {
            $error = $this->definedErrors[$error->getParentCode()];
        }

        return $error;
    }

    /**
     * @param int $parentCode
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
     * @param bool  $isParent
     *
     * @return bool
     */
    private function parentOf (Error $childError, Error $error, &$isParent) {

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