<?php


namespace Core\Validation;


use Core\Error\Error;
use Core\Error\ValidationException;
use Core\Parameter\UnsafeParameter;

class ValidationResult {

    /**
     * @var Error[]
     */
    protected $errors;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param array   $validatedParams
     * @param Error[] $errors
     *
     */
    public function __construct (array $validatedParams, array $errors = []) {

        $this->params = $validatedParams;
        $this->errors = $errors;

    }

    /**
     * @param string $field
     *
     * @return mixed
     */
    public function getValidatedValue ($field) {

        $validatedParams = $this->getValidatedParams();
        if (array_key_exists($field, $validatedParams)) {
            return $validatedParams[$field];
        }
        // TODO : what should i do if field is not present in params ?

    }

    /**
     * @return array
     */
    public function getValidatedParams () {

        return array_filter($this->params, function ($param) {

            return !($param instanceof UnsafeParameter);

        });

    }

    /**
     * @throws ValidationException
     */
    public function throwIfErrors () {

        if ($this->hasErrors()) {
            throw new ValidationException($this->getErrors());
        }

    }

    /**
     * @return bool
     */
    public function hasErrors () {

        return !!$this->getErrors();

    }

    /**
     * @return \Core\Error\Error[]
     */
    public function getErrors () {

        return $this->errors;

    }

}