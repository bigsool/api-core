<?php


namespace Core\Context;

use Core\Auth;
use Core\Parameter\UnsafeParameter;
use Traversable;

class ActionContext implements \ArrayAccess, \IteratorAggregate {

    /**
     * @var array
     */
    protected $params;

    /**
     * @var RequestContext|ActionContext
     */
    protected $parentContext;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $actionName;

    /**
     * @var ApplicationContext
     */
    protected $applicationContext;

    /**
     * @var array
     */
    protected $result = [];

    /**
     * @param RequestContext|ActionContext $context
     * @param string                       $module
     * @param string                       $actionName
     */
    protected function __construct ($context, $module, $actionName) {

        if ($context instanceof RequestContext) {
            $params = $this->convertToUnsafeParameter($context->getParams(), '');
        }
        elseif ($context instanceof ActionContext) {
            $params = $context->getParams();
        }
        else {
            throw new \RuntimeException('invalid context');
        }

        $this->module = $module;
        $this->actionName = $actionName;
        $this->parentContext = $context;
        $this->applicationContext = $context->getApplicationContext();
        $this->auth = $context->getAuth();
        $this->setParams($params);

    }

    /**
     * @param array $values
     *
     * @param       $path
     *
     * @return \Core\Parameter\UnsafeParameter[]
     */
    public function convertToUnsafeParameter (array $values, $path) {

        $params = [];
        foreach ($values as $key => $value) {
            $newPath = !empty($path) ? "$path.$key" : $key;
            $params[$key] =
                new UnsafeParameter(is_array($value) ? $this->convertToUnsafeParameter($value, $newPath) : $value,
                                    $newPath);
        }

        return $params;

    }

    /**
     * @param array $keys
     *
     * @return array
     */
    public function getParams (array $keys = NULL) {

        if (isset($keys)) {

            $params = [];
            foreach ($keys as $key) {
                $params[$key] = $this->getParam($key);
            }

            return $params;

        }

        return $this->params;
    }

    /**
     * @param array $params
     */
    public function setParams ($params) {

        $this->clearParams();

        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }

    }

    /**
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getParam ($key, $default = NULL) {

        $exploded = explode('.', $key);
        $params = $this->params;
        foreach ($exploded as $index => $key) {
            if (!isset($params[$key])) {
                return $default;
            }
            // it's not necessary to create an array for the last key
            if ($index + 1 == count($exploded)) {
                break;
            }
            $params = UnsafeParameter::getFinalValue($params[$key]);
        }

        return $params[$key];

    }

    /**
     * @param string $moduleName
     * @param string $actionName
     *
     * @return ActionContext
     */
    public function newDerivedContextFor ($moduleName, $actionName) {

        return new ActionContext($this, $moduleName, $actionName);

    }

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function process (array $params = []) {

        $action = $this->getApplicationContext()->getAction($this->module, $this->actionName);
        $this->setParams($params);

        return $action->process($this);

    }

    /**
     * @return ApplicationContext
     */
    public function getApplicationContext () {

        return $this->applicationContext;

    }

    /**
     * @param mixed $key
     */
    public function unsetParam ($key) {

        unset($this->params[$key]);

    }

    /**
     * @return Auth
     */
    public function getAuth () {

        return $this->auth;
    }

    /**
     *
     */
    public function clearParams () {

        $this->params = [];

    }

    /**
     * @param string $key
     * @param mixed  $defaultValue
     */
    public function setDefaultParam ($key, $defaultValue) {

        if (!array_key_exists($key, $this->params)) {
            $this->setParam($key, $defaultValue);
        }

    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function setParam ($key, $value) {

        if (!is_scalar($key)) {
            throw new \RuntimeException('invalid key type');
        }

        $exploded = explode('.', $key);
        $params = &$this->params;
        foreach ($exploded as $index => $key) {

            // it's not necessary to create an array for the last key
            if ($index + 1 == count($exploded)) {
                break;
            }

            if (!isset($params[$key])) {
                $params[$key] = [];
            }
            $params[$key] = UnsafeParameter::getFinalValue($params[$key]);
            $params = &$params[$key];

        }

        $params[$key] = $value;

    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getFinalParam ($key, $default = NULL) {

        return UnsafeParameter::getFinalValue($this->getParam($key, $default));

    }

    /**
     * @param mixed $key
     *
     * @return mixed
     */
    public function getVerifiedParam ($key) {

        $params = $this->getVerifiedParams();

        return isset($params[$key]) ? $params[$key] : NULL;

    }

    /**
     * @param array $keys
     *
     * @return array
     */
    public function getVerifiedParams (array $keys = NULL) {

        $verifiedParams = [];

        foreach ($this->getParams() as $key => $param) {
            if (!($param instanceof UnsafeParameter) && (!$keys || in_array($key, $keys, true))) {
                $verifiedParams[$key] = $param;
            }
        }

        return $verifiedParams;
    }

    /**
     *
     */
    public function clearVerifiedParams () {

        foreach ($this->params as $key => $param) {
            if (!($param instanceof UnsafeParameter)) {
                unset($this->params[$key]);
            }
        }

    }

    /**
     * @return RequestContext
     */
    public function getRequestContext () {

        $context = $this;
        while (!($context instanceof RequestContext)) {
            $context = $context->getParentContext();
        }

        return $context;

    }

    /**
     * @return RequestContext|ActionContext
     */
    public function getParentContext () {

        return $this->parentContext;

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

        return array_key_exists($offset, $this->result);

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

        if (!isset($this->result[$offset]) && $this->getParentContext() instanceof ActionContext) {
            return $this->getParentContext()->offsetGet($offset);
        }

        return $this->result[$offset];

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

        $this->result[$offset] = $value;

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

        unset($this->result[$offset]);

    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Retrieve an external iterator
     * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
     * @return Traversable An instance of an object implementing <b>Iterator</b> or
     * <b>Traversable</b>
     */
    public function getIterator () {

        return new \ArrayIterator($this->result);

    }

}