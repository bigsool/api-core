<?php


namespace Core\Context;


use Core\Auth;
use Core\Error\FormattedError;
use Core\Field\KeyPath;
use Core\Filter\Filter;
use Core\Filter\StringFilter;
use Core\Parameter\UnsafeParameter;

class RequestContext {

    /**
     * @var array
     */
    protected $params = [];

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
     * @var string
     */
    protected $returnedRootEntity;

    /**
     * @var KeyPath[]
     */
    protected $returnedKeyPaths = [];

    /**
     * @var Filter
     */
    protected $filter;

    /**
     */
    public function __construct () {

        $this->auth = new Auth();

    }

    /**
     * @return Filter
     */
    public function getFilter () {

        return $this->filter;

    }

    /**
     * @param Filter $filter
     */
    public function setFilter (Filter $filter) {

        $this->filter = $filter;

    }

    /**
     * @return KeyPath[]
     */
    public function getReturnedKeyPaths () {

        return $this->returnedKeyPaths;

    }

    /**
     * @param KeyPath[] $returnedKeyPaths
     */
    public function setReturnedKeyPaths (array $returnedKeyPaths) {

        foreach ($returnedKeyPaths as $returnedKeyPath) {
            if (!($returnedKeyPath instanceof KeyPath)) {
                throw new \RuntimeException('invalid $returnedKeyPath');
            }
        }

        $this->returnedKeyPaths = $returnedKeyPaths;

    }

    /**
     * @return string
     */
    public function getReturnedRootEntity () {

        return $this->returnedRootEntity;

    }

    /**
     * @param string $returnedRootEntity
     */
    public function setReturnedRootEntity ($returnedRootEntity) {

        if (!is_string($returnedRootEntity)) {
            throw new \RuntimeException('invalid $returnedRootEntity');
        }

        $this->returnedRootEntity = $returnedRootEntity;

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

        FormattedError::setLang($locale);
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

        if (isset($params['auth'])) {
            // TODO: replace that part by the real authentication system
            $findCtx = new FindQueryContext('User', $this);
            $findCtx->addFilter(new StringFilter('User', '', 'id = :id'));
            $findCtx->addKeyPath(new KeyPath('*'));
            $findCtx->setParams(['id' => $params['auth']]);
            $users = ApplicationContext::getInstance()->getNewRegistry()->find($findCtx, false);
            if (count($users) == 1) {
                $this->getAuth()->setUser($users[0]);
            }
            unset($params['auth']);
        }

        $this->params = $params;

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
     * @param mixed $key
     *
     * @return mixed
     */
    public function getParam ($key) {

        return isset($this->params[$key]) ? $this->params[$key] : NULL;

    }

}