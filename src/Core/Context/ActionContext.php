<?php


namespace Core\Context;

use Core\Auth;
use Core\Parameter\Parameter;
use Core\Parameter\SafeParameter;
use Core\Parameter\UnsafeParameter;

class ActionContext extends \ArrayObject {

    /**
     * @var Parameter[]
     */
    protected $params;

    /**
     * @var SafeParameter[]
     */
    protected $verifiedParams = [];

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
     * @return Parameter[]
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
     * @param Parameter[] $params
     */
    public function setParams ($params) {

        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }

    }

    public function clearParams () {

        $this->params = [];
        $this->verifiedParams = [];

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
     * @return Auth
     */
    public function getAuth () {

        return $this->auth;
    }

    /**
     * @param string    $key
     * @param Parameter $value
     */
    public function setParam ($key, Parameter $value) {

        if (!is_scalar($key)) {
            throw new \RuntimeException('invalid key type');
        }

        $this->params[$key] = $value;

    }

    /**
     * @param array $keys
     *
     * @return SafeParameter[]
     */
    public function getVerifiedParams (array $keys = NULL) {

        if (isset($keys)) {

            $verifiedParams = [];
            foreach ($keys as $key) {
                $verifiedParams[$key] = $this->getVerifiedParam($key);
            }

            return $verifiedParams;

        }

        return $this->verifiedParams;
    }

    /**
     * @param SafeParameter[] $verifiedParams
     */
    public function setVerifiedParams ($verifiedParams) {

        foreach ($verifiedParams as $key => $value) {
            $this->setVerifiedParam($key, $value);
        }

    }

    /**
     * @param mixed $key
     *
     * @return SafeParameter
     */
    public function getVerifiedParam ($key) {

        return isset($this->verifiedParams[$key]) ? $this->verifiedParams[$key] : NULL;

    }

    /**
     * @param string        $key
     * @param SafeParameter $value
     */
    public function setVerifiedParam ($key, SafeParameter $value) {

        if (!is_scalar($key)) {
            throw new \RuntimeException('invalid key type');
        }

        $this->verifiedParams[$key] = $value;

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

}