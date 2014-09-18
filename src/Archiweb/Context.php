<?php


namespace Archiweb;


use Archiweb\Parameter\Parameter;

class Context implements \ArrayAccess {

    /**
     * @var array
     */
    protected $array = array();

    /**
     * @var Parameter[]
     */
    protected $params = array();

    /**
     * @return Parameter[]
     */
    public function getParams () {

        return $this->params;

    }

    /**
     * @param Parameter[] $params
     */
    public function setParams (array $params) {

        foreach ($params as $param) {
            if (!($param instanceof Parameter)) {
                throw new \RuntimeException('invalid type');
            }
        }

        $this->params = $params;

    }

    /**
     * @param mixed $key
     *
     * @return Parameter
     */
    public function getParam ($key) {

        return isset($this->params[$key]) ? $this->params[$key] : NULL;

    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     */
    public function offsetExists ($offset) {

        return isset($this->array[$offset]);

    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     */
    public function offsetGet ($offset) {

        return $this->array[$offset];

    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value  <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     */
    public function offsetSet ($offset, $value) {

        $this->array[$offset] = $value;

    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     */
    public function offsetUnset ($offset) {

        unset($this->array[$offset]);

    }
}