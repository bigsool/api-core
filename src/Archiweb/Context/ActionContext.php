<?php


namespace Archiweb\Context;

use Archiweb\Parameter\Parameter;

class ActionContext extends \ArrayObject implements ApplicationContextProvider {

    /**
     * @var Parameter[]
     */
    protected $params;

    /**
     * @var RequestContext
     */
    protected $requestContext;

    /**
     * @param RequestContext $requestContext
     */
    public function __construct (RequestContext $requestContext) {

        parent::__construct();

        $this->requestContext = $requestContext;

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
     * @return ApplicationContext
     */
    public function getApplicationContext () {

        return $this->getRequestContext()->getApplicationContext();

    }

    /**
     * @return RequestContext
     */
    public function getRequestContext () {

        return $this->requestContext;

    }

}