<?php


namespace Archiweb\Context;


use Archiweb\Parameter\UnsafeParameter;

class RequestContext implements ApplicationContextProvider {

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var ApplicationContext
     */
    protected $applicationContext;

    /**
     * @param ApplicationContext $context
     */
    public function __construct (ApplicationContext $context) {

        $this->applicationContext = $context;

    }

    /**
     * @return ActionContext
     */
    public function getNewActionContext () {

        $actonContext = new ActionContext($this);

        $params = [];
        foreach ($this->getParams() as $key => $param) {
            $params[] = new UnsafeParameter($param);
        }

        $actonContext->setParams($params);

        return $actonContext;

    }

    /**
     * @return array
     */
    public function getParams () {

        return $this->params;

    }

    /**
     * @param array $params
     */
    public function setParams (array $params) {

        $this->params = $params;

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
     * @return ApplicationContext
     */
    public function getApplicationContext () {

        return $this->applicationContext;

    }
}