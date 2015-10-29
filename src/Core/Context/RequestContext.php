<?php


namespace Core\Context;


use Archipad\RightsManager;
use Core\Auth;
use Core\Error\FormattedError;
use Core\Field\RelativeField;
use Core\Filter\Filter;
use Core\Model\Credential;
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

    protected $authToken;

    /**
     */
    public function __construct () {

        $this->auth = new Auth();

    }

    /**
     * @param RequestContext $current
     *
     * @return RequestContext
     */
    public static function createNewInternalRequestContext (RequestContext $current = NULL) {

        $reqCtx = new static;

        if ($current) {

            $reqCtx->setLocale($current->getLocale());
            $reqCtx->setClientVersion($current->getClientVersion());
            $reqCtx->setClientName($current->getClientName());
            $reqCtx->setIpAddress($current->getIpAddress());
            $reqCtx->setAuthToken($current->getAuthToken());

        }

        $reqCtx->setAuth(Auth::createInternalAuth());

        return $reqCtx;

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
    public function getIpAddress () {

        return $this->ipAddress;

    }

    /**
     * @param string $ipAddress
     */
    public function setIpAddress ($ipAddress) {

        $this->ipAddress = (string)$ipAddress;

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
     * @param RelativeField[] $returnedFields
     */
    public function addReturnedFields (array $returnedFields) {

        $this->setReturnedFields(array_merge($this->getReturnedFields(), $returnedFields));

    }

    /**
     * @param RelativeField $formattedReturnedField
     *
     * @throws FormattedError
     */
    public function addFormattedReturnedField (RelativeField $formattedReturnedField) {

        $this->verifyFields([$formattedReturnedField]);

        $this->formattedReturnedFields[] = $formattedReturnedField;

    }

    /**
     * @param RelativeField $returnedField
     *
     * @throws FormattedError
     */
    public function addReturnedField (RelativeField $returnedField) {

        $this->verifyFields([$returnedField]);

        $this->returnedFields[] = $returnedField;

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

        if ($this->authToken) {

            $authToken = $this->authToken;

            $appCtx = $this->getApplicationContext();
            $checkAuthCtx = $appCtx->getActionContext(new RequestContext(), 'Core\Credential', 'checkAuth');
            $checkAuthCtx->setParams(['authToken' => new UnsafeParameter($authToken, 'authToken')]);
            $appCtx = ApplicationContext::getInstance();
            /**
             * @var Credential[] $credentials
             */
            $credentials = $appCtx->getAction('Core\Credential', 'checkAuth')->process($checkAuthCtx);
            $auth = new Auth();
            $auth->setCredential($credentials[0]);
            if (in_array($credentials[0]->getLogin(), RightsManager::getRootLogins())) {
                $auth->addRootRight();
            }
            if (count($credentials) == 2) {
                $auth->setSuperUserCredential($credentials[1]);
            }
            $this->setAuth($auth);

            $setAuthAction = $appCtx->getAction('Core\Credential', 'renewAuthCookie');
            $appCtx->getOnSuccessActionQueue()
                   ->addAction($setAuthAction, ['authToken' => $authToken, 'credentials' => $credentials]);
            unset($params['authToken']);

        }

        $this->params = $params;

    }

    /**
     * @return ApplicationContext
     */
    public function getApplicationContext () {

        return ApplicationContext::getInstance();

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

    public function newFindQueryContext ($entityName, $keepRequestedFields = true) {

        $reqCtx = $keepRequestedFields ? $this : $this->copyWithoutRequestedFields();

        return new FindQueryContext($entityName, $reqCtx);

    }

    /**
     * @return RequestContext
     */
    public function copyWithoutRequestedFields () {

        $reqCtx = clone $this;
        $reqCtx->setReturnedFields([]);
        $reqCtx->setFormattedReturnedFields([]);

        return $reqCtx;

    }

    public function setAuthToken ($authToken) {

        $this->authToken = $authToken;

    }

    public function getAuthToken () {

        return $this->authToken;

    }

}