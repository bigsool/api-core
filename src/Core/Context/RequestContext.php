<?php


namespace Core\Context;


use Core\Auth;
use Core\Error\FormattedError;
use Core\Field\KeyPath;
use Core\Filter\Filter;
use Core\Module\Credential\Helper;
use Core\Parameter\UnsafeParameter;
use Symfony\Component\HttpFoundation\Response;

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
     * @var KeyPath[]
     */
    protected $formattedReturnedKeyPaths = [];

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var string
     */
    protected $ipAddress;

    /**
     * @var Response
     */
    protected $response;

    /**
     */
    public function __construct () {

        $this->auth = new Auth();

    }

    /**
     * @return Response
     */
    public function getResponse () {

        return $this->response;

    }

    /**
     * @param Response $response
     */
    public function setResponse (Response $response) {

        $this->response = $response;

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
     *
     * @throws FormattedError
     */
    public function setReturnedKeyPaths (array $returnedKeyPaths) {

        foreach ($returnedKeyPaths as $returnedKeyPath) {

            if (!($returnedKeyPath instanceof KeyPath)) {
                throw new \RuntimeException('invalid $returnedKeyPath');
            }

            $value = $returnedKeyPath->getValue();
            if (!is_string($value) || $value == '*') {
                throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERROR_BAD_FIELD);
            }

        }

        $this->returnedKeyPaths = $returnedKeyPaths;

    }

    /**
     * @return KeyPath[]
     */
    public function getFormattedReturnedKeyPaths () {

        if (count($this->formattedReturnedKeyPaths) == 0) {
            return $this->getReturnedKeyPaths();
        }

        return $this->formattedReturnedKeyPaths;

    }

    /**
     * @param KeyPath[] $formattedReturnedKeyPaths
     *
     * @throws FormattedError
     */
    public function setFormattedReturnedKeyPaths (array $formattedReturnedKeyPaths) {

        foreach ($formattedReturnedKeyPaths as $formattedReturnedKeyPath) {
            if (!($formattedReturnedKeyPath instanceof KeyPath)) {
                throw new \RuntimeException('invalid $returnedKeyPath');
            }

            $value = $formattedReturnedKeyPath->getValue();
            if (!is_string($value) || $value == '*') {
                throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERROR_BAD_FIELD);
            }
        }

        $this->formattedReturnedKeyPaths = $formattedReturnedKeyPaths;

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
            $params[$key] = new UnsafeParameter($param, $key);
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

        if (isset($params['authToken'])) {
            $checkAuthCtx = new ActionContext(new RequestContext());
            $checkAuthCtx->setParams(['authToken' => new UnsafeParameter(json_decode($params['authToken'], true),'authToken')]);
            $appCtx = ApplicationContext::getInstance();
            $cred = $appCtx->getAction('Core\Credential', 'checkAuth')->process($checkAuthCtx);
            $auth = new Auth();
            $auth->setCredential($cred);
            $this->setAuth($auth);
            
            $helper = new Helper();
            $authToken = $helper->getNewAuthToken($params['authToken']['login']);
            $setAuthAction = $appCtx->getAction('Core\Credential','setAuthCookie');
            $appCtx->getOnSuccessActionQueue()->enqueue($setAuthAction, ['authToken' => $authToken]);
            
            unset($params['authToken']);
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

    /**
     * @return string
     */
    public function getIpAddress () {

        return $this->ipAddress;

    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress ($ipAddress) {

        $this->ipAddress = (string)$ipAddress;

    }

}