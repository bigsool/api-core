<?php


namespace Archiweb\Context;

use Archiweb\Parameter\Parameter;
use Archiweb\Parameter\UnsafeParameter;

class ActionContext extends \ArrayObject implements ApplicationContextProvider {

    /**
     * @var Parameter[]
     */
    protected $params;

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
     * @return Parameter[]
     */
    public function getParams () {

        return $this->params;
    }

    /**
     * @param Parameter[] $params
     */
    public function setParams ($params) {

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