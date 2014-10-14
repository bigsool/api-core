<?php


namespace Archiweb\Context;

use Archiweb\Parameter\Parameter;
use Archiweb\Parameter\SafeParameter;
use Archiweb\Parameter\UnsafeParameter;

class ActionContext extends \ArrayObject implements ApplicationContextProvider {

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
     * @param RequestContext|ActionContext $context
     */
    public function __construct ($context) {

        parent::__construct();

        $params = [];
        if ($context instanceof RequestContext) {
            foreach ($context->getParams() as $key => $value) {
                $params[$key] = new UnsafeParameter($value);
            }
        }
        elseif ($context instanceof ActionContext) {
            $params = $context->getParams();
        }
        else {
            throw new \RuntimeException('invalid context');
        }

        $this->parentContext = $context;
        $this->setParams($params);

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

    /**
     * @param mixed $key
     *
     * @return Parameter
     */
    public function getParam ($key) {

        return isset($this->params[$key]) ? $this->params[$key] : NULL;

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
     * @param mixed $key
     *
     * @return SafeParameter
     */
    public function getVerifiedParam ($key) {

        return isset($this->verifiedParams[$key]) ? $this->verifiedParams[$key] : NULL;

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
     * @return ApplicationContext
     */
    public function getApplicationContext () {

        return $this->getParentContext()->getApplicationContext();

    }

    /**
     * @return RequestContext
     */
    public function getParentContext () {

        return $this->parentContext;

    }

}