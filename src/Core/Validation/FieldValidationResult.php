<?php


namespace Core\Validation;


use Core\Error\Error;
use Core\Error\ValidationException;

// TODO : refactor error part is the same as ValidationResult
class FieldValidationResult {

    /**
     * @var string
     */
    protected $field;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var Error[]
     */
    protected $errors;

    /**
     * @param string  $field
     * @param mixed   $value
     * @param Error[] $errors
     */
    public function __construct ($field, $value, array $errors) {

        $this->field;
        $this->value = $value;
        $this->errors = $errors;

    }

    /**
     * @return string
     */
    public function getField () {

        return $this->field;

    }

    /**
     * @return mixed
     */
    public function getValue () {

        return $this->value;

    }

    /**
     * @return Error[]
     */
    public function getErrors () {

        return $this->errors;

    }

    /**
     * @return bool
     */
    public function hasErrors() {

        return !!$this->errors;

    }

    /**
     * @throws ValidationException
     */
    public function throwIfErrors () {

        if ($this->hasErrors()) {
            throw new ValidationException($this->getErrors());
        }

    }

}