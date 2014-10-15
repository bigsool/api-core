<?php


namespace Archiweb\Context;


use Archiweb\Auth;
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
     * @var string
     */
    protected $clientName;

    /**
     * @var string
     */
    protected $clientVersion;

    /**
     * @var string
     */
    protected $locale;

    /**
     * @var Auth
     */
    protected $auth;

    /**
     * @param ApplicationContext $context
     */
    public function __construct (ApplicationContext $context) {

        $this->applicationContext = $context;
        $this->auth = new Auth();

    }

    /**
     * @return Auth
     */
    public function getAuth () {

        return $this->auth;
    }

    /**
     * @param Auth $auth
     */
    public function setAuth (Auth $auth) {

        $this->auth = $auth;
    }

    /**
     * @return string
     */
    public function getClientName () {

        return $this->clientName;
    }

    /**
     * @param string $clientName
     */
    public function setClientName ($clientName) {

        $this->clientName = $clientName;
    }

    /**
     * @return string
     */
    public function getClientVersion () {

        return $this->clientVersion;
    }

    /**
     * @param string $clientVersion
     */
    public function setClientVersion ($clientVersion) {

        $this->clientVersion = $clientVersion;
    }

    /**
     * @return string
     */
    public function getLocale () {

        return $this->locale;
    }

    /**
     * @param string $locale
     */
    public function setLocale ($locale) {

        $this->locale = $locale;
    }

    /**
     * @return ActionContext
     */
    public function getNewActionContext () {

        $actonContext = new ActionContext($this);

        $params = [];
        foreach ($this->getParams() as $key => $param) {
            $params[$key] = new UnsafeParameter($param);
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