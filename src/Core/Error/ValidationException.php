<?php


namespace Core\Error;

    // TODO : rename it UpsertContextValidationException ?
// might be used for Action validation exceptions
class ValidationException extends \Exception {

    /**
     * @var Error[]
     */
    protected $errors = [];

    /**
     * @param Error[] $errors
     */
    public function __construct (array $errors) {

        $this->addErrors($errors);

        parent::__construct();

    }

    /**
     * @param Error[] $errors
     */
    public function addErrors (array $errors) {

        foreach ($errors as $error) {
            $this->addError($error);
        }
    }

    /**
     * @param Error $error
     */
    public function addError (Error $error) {

        $this->errors[] = $error;

    }

    /**
     * @return Error[]
     */
    public function getErrors () {

        return $this->errors;

    }

}