<?php


namespace Core\Parameter;


class UnsafeParameter {

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $path;

    /**
     * @param mixed  $value
     * @param string $path
     */
    public function __construct ($value, $path) {

        $this->value = $value;
        $this->path = $path;

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
     * @return string
     */
    public function getPath () {

        return $this->path;
    }

    /**
     * @return bool
     */
    public function isSafe () {

        return false;

    }
}