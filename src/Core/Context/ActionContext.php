<?php


namespace Core\Context;

use Core\Auth;
use Core\Parameter\UnsafeParameter;

class ActionContext extends \ArrayObject {

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
     * @param RequestContext|ActionContext $context
     */
    public function __construct ($context) {

        parent::__construct();

        if ($context instanceof RequestContext) {
            $params = $this->convertToUnsafeParameter($context->getParams());
        }
        elseif ($context instanceof ActionContext) {
            $params = $context->getParams();
        }
        else {
            throw new \RuntimeException('invalid context');
        }

        $this->parentContext = $context;
        $this->auth = $context->getAuth();
        $this->setParams($params);

    }

    /**
     * @param array $values
     *
     * @return UnsafeParameter[]
     */
    public function convertToUnsafeParameter (array $values) {

        $params = [];
        foreach ($values as $key => $value) {
            $params[$key] = new UnsafeParameter(is_array($value) ? $this->convertToUnsafeParameter($value) : $value);
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
     *
     * @return mixed
     */
    public function getParam ($key) {

        return isset($this->params[$key]) ? $this->params[$key] : NULL;

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
     * @param mixed  $value
     */
    public function setParam ($key, $value) {

        if (!is_scalar($key)) {
            throw new \RuntimeException('invalid key type');
        }

        $this->params[$key] = $value;

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
     * @param mixed $key
     *
     * @return mixed
     */
    public function getVerifiedParam ($key) {

        $params = $this->getVerifiedParams();

        return isset($params[$key]) ? $params[$key] : NULL;

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
     * @inheritdoc
     */
    public function offsetGet ($index) {

        if (!isset($this[$index]) && $this->getParentContext() instanceof ActionContext) {
            return $this->getParentContext()->offsetGet($index);
        }

        return parent::offsetGet($index);

    }

}