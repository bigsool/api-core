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
     * @var mixed
     */
    protected $unsafeValue;

    /**
     * @param mixed  $unsafeValue
     * @param mixed  $value
     * @param string $path
     */
    public function __construct ($unsafeValue, $value, $path) {

        $this->value = $value;
        $this->path = $path;
        $this->unsafeValue = $unsafeValue;
    }

    /**
     * @param array  $params
     * @param string $field
     *
     * @return mixed
     */
    public static function findFinalValue (array &$params, $field) {

        return isset($params[$field]) ? UnsafeParameter::getFinalValue($params[$field]) : NULL;

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
     * Recursively finalize given value.
     *
     * @param $param
     *
     * @return mixed
     */
    public static function getRecursiveFinalValue ($param) {

        if ($param instanceof UnsafeParameter) {
            return $param->getUnsafeValue();
        }
        if (is_array($param)) {
            $unsafeParam = [];
            foreach ($param as $key => $value) {
                if ($value instanceof UnsafeParameter) {
                    $unsafeParam[$key] = $value->getUnsafeValue();
                } else {
                    $unsafeParam[$key] = $value;
                }
            }
            return $unsafeParam;
        }
        return $param;

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

    /**
     * @return mixed
     */
    protected function getUnsafeValue () {

        return $this->unsafeValue;
    }
}