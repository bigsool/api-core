<?php


namespace Core\Parameter;


class UnsafeParameter {

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
     * @param $param
     *
     * @return mixed
     */
    public static function getFinalValue ($param) {

        return ($param instanceof UnsafeParameter) ? $param->getValue() : $param;

    }

    /**
     * @return mixed
     */
    public function getValue () {

        return $this->value;

    }

    /**
     * @return bool
     */
    public function isSafe () {

        return false;

    }
}