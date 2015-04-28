<?php


namespace Core\Module\Credential;

use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Field\RelativeField;
use Core\Filter\Filter;
use Core\Model\Credential;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    const AUTH_TOKEN_BASIC = 'basic';

    const AUTH_TOKEN_RESET_PASSWORD = 'resetPassword';

    /**
     * @param ActionContext $actionContext
     * @param array         $params
     *
     * @return array
     * @throws \Core\Error\FormattedError
     */
    public function login (ActionContext $actionContext, array $params) {

        $appCtx = ApplicationContext::getInstance();

        /**
         * @var Credential $credentials
         */
        $credential = $this->getCredentialFromLogin($params['login']);

        if (is_null($credential)) {
            throw $appCtx->getErrorManager()->getFormattedError(ERROR_USER_NOT_FOUND);
        }

        if (!password_verify($params['password'], $credential->getPassword())) {
            throw $appCtx->getErrorManager()->getFormattedError(ERROR_PERMISSION_DENIED);
        }

        $expiration = time() + $appCtx->getConfigManager()->getConfig()['expirationAuthToken'];

        $this->createLoginHistory($actionContext, $credential, $params['loginHistory']);

        return $this::generateAuthToken($params['login'], $expiration, $credential->getPassword(),
                                        self::AUTH_TOKEN_BASIC);

    }

    /**
     * @param string $login
     *
     * @return Credential|null
     */
    public function getCredentialFromLogin ($login) {

        $appCtx = ApplicationContext::getInstance();

        $registry = $appCtx->getNewRegistry();

        $qryCtx = new FindQueryContext('Credential', RequestContext::createNewInternalRequestContext());

        $qryCtx->addField(new RelativeField('*'));

        $qryCtx->addFilter($appCtx->getFilterByEntityAndName('Credential', 'filterByLogin'));

        $qryCtx->setParams(['login' => $login]);

        $credentials = $registry->find($qryCtx);

        return count($credentials) != 1 ? NULL : $credentials[0];

    }

    /**
     * @param ActionContext $actionContext
     * @param               $credential
     * @param array         $params
     */
    protected function createLoginHistory (ActionContext $actionContext, $credential, array $params) {

        $this->checkRealModelType($credential, 'Credential');

        $reqCtx = $actionContext->getRequestContext();

        $loginHistory = $this->createRealModel('LoginHistory');

        $params['credential'] = $credential;
        $params['date'] = new \DateTime();
        $params['clientName'] = $reqCtx->getClientName();
        $params['clientVersion'] = $reqCtx->getClientVersion();
        $params['IP'] = $reqCtx->getIpAddress();

        $this->basicSave($loginHistory, $params);

        $actionContext['loginHistory'] = $loginHistory;

    }

    /**
     * @param string $login
     * @param string $expiration
     * @param string $hashedPassword
     * @param string $type
     *
     * @return array
     */
    private function generateAuthToken ($login, $expiration, $hashedPassword, $type) {

        return ['hash'       => sha1($login . $expiration . $hashedPassword . $type),
                'login'      => $login,
                'expiration' => $expiration,
                'type'       => $type
        ];

    }

    /**
     * @param mixed   $authToken
     * @param integer $credentialId
     *
     * @return array
     * @throws \Core\Error\FormattedError
     */
    public function renewAuthToken ($authToken, $credentialId) {

        $appCtx = ApplicationContext::getInstance();
        $errorManager = $appCtx->getErrorManager();

        $this->checkAuthTokenStructure($authToken);

        $credential = $this->getCredentialFromId($credentialId);

        if (is_null($credential)) {
            throw $errorManager->getFormattedError(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        $expiration = time() + $appCtx->getConfigManager()->getConfig()['expirationAuthToken'];

        return self::generateAuthToken($credential->getLogin(), $expiration, $credential->getPassword(),
                                       $authToken['type']);

    }

    /**
     * @param mixed $authToken
     *
     * @throws \Core\Error\FormattedError
     */
    private function checkAuthTokenStructure ($authToken) {

        $errorManager = ApplicationContext::getInstance()->getErrorManager();

        if (!is_array($authToken) || !isset($authToken['login']) || !isset($authToken['expiration'])
            || !isset($authToken['type'])
        ) {
            throw $errorManager->getFormattedError(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

    }

    /**
     * @param integer $id
     *
     * @return Credential|null
     */
    public function getCredentialFromId ($id) {

        $appCtx = ApplicationContext::getInstance();

        $registry = $appCtx->getNewRegistry();

        $qryCtx = new FindQueryContext('Credential', RequestContext::createNewInternalRequestContext());

        $qryCtx->addField(new RelativeField('*'));

        $qryCtx->addFilter($appCtx->getFilterByEntityAndName('Credential', 'filterById'));

        $qryCtx->setParams(['id' => $id]);

        $credentials = $registry->find($qryCtx);

        return count($credentials) != 1 ? NULL : $credentials[0];

    }

    /**
     * @param Credential $credential
     * @param string     $authTokenType
     *
     * @return array
     */
    public function getNewAuthToken ($credential, $authTokenType, $expiration) {

        $appCtx = ApplicationContext::getInstance();

        if (!$expiration) {
            $expiration = time() + $appCtx->getConfigManager()->getConfig()['expirationAuthToken'];
        }

        return self::generateAuthToken($credential->getLogin(), $expiration, $credential->getPassword(),
                                       $authTokenType);

    }

    /**
     * @param ActionContext $actionContext
     * @param array         $params
     *
     */
    public function createCredential (ActionContext $actionContext, array $params) {

        $credential = $this->createRealModel('Credential');

        $params['password'] = password_hash($params['password'], PASSWORD_BCRYPT);

        $this->basicSave($credential, $params);

        $actionContext['credential'] = $credential;

    }

    /**
     * @param ActionContext $actCtx
     * @param               $credential
     * @param array         $params
     */
    public function updateCredential (ActionContext $actCtx, $credential, array $params) {

        $this->checkRealModelType($credential, 'Credential');

        $params['password'] = password_hash($params['password'], PASSWORD_BCRYPT);

        $this->basicSave($credential, $params);

        $actCtx['credential'] = $credential;

    }

    /**
     * @param ActionContext   $actionContext
     * @param RelativeField[] $keyPaths
     * @param Filter[]        $filters
     * @param array           $params
     */
    public function findCredential (ActionContext $actionContext, array $keyPaths = [], array $filters = [],
                                    array $params = []) {

        $qryCtx = new FindQueryContext('Credential', $actionContext->getRequestContext());

        $actionContext['credentials'] = $this->basicFind($qryCtx, $keyPaths, $filters, $params);

    }

    /**
     * @param mixed $authToken
     *
     * @return Credential
     * @throws \Core\Error\FormattedError
     */
    public function checkAuthToken ($authToken) {

        $errorManager = ApplicationContext::getInstance()->getErrorManager();

        $this->checkAuthTokenStructure($authToken);

        $login = $authToken['login'];

        $expiration = $authToken['expiration'];
        $type = $authToken['type'];

        if ($expiration < time() || !($credential = $this->getCredentialFromLogin($login))) {
            throw $errorManager->getFormattedError(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        $authTokenGenerated = $this->generateAuthToken($login, $expiration, $credential->getPassword(), $type);

        if ($authTokenGenerated != $authToken) {
            throw $errorManager->getFormattedError(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        return $credential;

    }

}