<?php


namespace Core\Context;


use Core\Auth;
use Core\Error\FormattedError;
use Core\Field\RelativeField;
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
     * @var RelativeField[]
     */
    protected $returnedFields = [];

    /**
     * @var RelativeField[]
     */
    protected $formattedReturnedFields = [];

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
     * @return RelativeField[]
     */
    public function getFormattedReturnedFields () {

        if (count($this->formattedReturnedFields) == 0) {
            return $this->getReturnedFields();
        }

        return $this->formattedReturnedFields;

    }

    /**
     * @param RelativeField[] $formattedReturnedFields
     *
     * @throws FormattedError
     */
    public function setFormattedReturnedFields (array $formattedReturnedFields) {

        $this->verifyFields($formattedReturnedFields);

        $this->formattedReturnedFields = $formattedReturnedFields;

    }

    /**
     * @return RelativeField[]
     */
    public function getReturnedFields () {

        return $this->returnedFields;

    }

    /**
     * @param RelativeField[] $returnedFields
     *
     * @throws FormattedError
     */
    public function setReturnedFields (array $returnedFields) {

        $this->verifyFields($returnedFields);

        $this->returnedFields = $returnedFields;

    }

    /**
     * @param array $returnedFields
     *
     * @throws FormattedError
     */
    protected function verifyFields (array $returnedFields) {

        foreach ($returnedFields as $returnedField) {

            if (!($returnedField instanceof RelativeField)) {
                throw new \RuntimeException('invalid $returnedField');
            }

            $value = $returnedField->getValue();
            if (!is_string($value) || $value == '*') {
                throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERROR_BAD_FIELD);
            }

        }
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
            $authToken = json_decode($params['authToken'], true);
            $checkAuthCtx = new ActionContext(new RequestContext());
            $checkAuthCtx->setParams(['authToken' => new UnsafeParameter($authToken, 'authToken')]);
            $appCtx = ApplicationContext::getInstance();
            $cred = $appCtx->getAction('Core\Credential', 'checkAuth')->process($checkAuthCtx);
            $auth = new Auth();
            $auth->setCredential($cred);
            $this->setAuth($auth);

            $helper = new Helper();
            $authToken = $helper->getNewAuthToken($authToken);
            $setAuthAction = $appCtx->getAction('Core\Credential', 'setAuthCookie');
            $appCtx->getOnSuccessActionQueue()->addAction($setAuthAction, ['authToken' => $authToken]);

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