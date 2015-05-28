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
     * @param int    $errorCode
     * @param string $field
     *
     * @return FormattedError
     */
    public function getFormattedError ($errorCode = NULL, $field = NULL) {

        if ($errorCode === NULL) {
            $errors = $this->errors;
        }
        else {
            $error = clone $this->getErrorForErrorCode($errorCode);
            if (!is_null($field)) {
                $error->setField($field);
            }
            $errors = [$error];
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
     * @param int         $errorCode
     * @param string|null $field
     *
     * @return Error
     */
    public function getError ($errorCode, $field = NULL) {

        $error = clone $this->getErrorForErrorCode($errorCode);
        if (isset($field)) {
            $error->setField($field);
        }

        return $error;

    }

    /**
     * @param Error $error
     *
     * @return FormattedError
     */
    private function buildFormattedError (Error $error) {

        $formattedError = new FormattedError($error);

        return $formattedError;

    }

    /**
     * @param int    $errorCode
     * @param string $field
     */
    public function addError ($errorCode, $field = NULL) {

        $error = clone $this->getErrorForErrorCode($errorCode);
        if (isset($field)) {
            $error->setField($field);
        }
        if (!in_array($error, $this->errors, true)) {
            $this->errors[] = $error;
        }

    }

    /**
     * @param Error[] $errors
     */
    public function addErrors(array $errors) {

        foreach ($errors as $error) {
            $this->errors[] = $error;
        }

    }

}