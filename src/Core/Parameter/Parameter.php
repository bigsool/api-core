<?php


namespace Core\Parameter;


abstract class Parameter {

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param mixed $value
     */
    public function __construct ($value) {

        $this->value = $value;

    }

    /**
     * @return bool
     */
    public abstract function isSafe ();

    /**
     * @return mixed
     */
    public function getValue () {

        return $this->value;

    }

}