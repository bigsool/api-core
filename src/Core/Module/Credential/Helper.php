<?php


namespace Core\Module\Credential;

use Core\Auth;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Field\RelativeField;
use Core\Filter\Filter;
use Core\Model\Credential;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    const AUTH_TOKEN_TYPE_BASIC = 'basic';

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
                                        self::AUTH_TOKEN_TYPE_BASIC);

    }

    /**
     * @param string $login
     *
     * @return Credential|null
     */
    public function getCredentialFromLogin ($login) {

        $appCtx = ApplicationContext::getInstance();

        $registry = $appCtx->getNewRegistry();

        $qryCtx = new FindQueryContext('Credential', new RequestContext(), [Auth::INTERNAL]);

        $qryCtx->addField(new RelativeField('*'));

        $qryCtx->addFilter($appCtx->getFilterByEntityAndName('Credential', 'filterByLogin'));

        $qryCtx->setParams(['login' => $login]);

        $credentials = $registry->find($qryCtx, false);

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
     * @param mixed $authToken
     *
     * @return array
     * @throws \Core\Error\FormattedError
     */
    public function getNewAuthToken ($authToken) {

        $appCtx = ApplicationContext::getInstance();
        $errorManager = $appCtx->getErrorManager();

        $this->checkAuthTokenStructure($authToken);

        $credential = $this->getCredentialFromLogin($authToken['login']);

        if (is_null($credential)) {
            throw $errorManager->getFormattedError(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        $expiration = time() + $appCtx->getConfigManager()->getConfig()['expirationAuthToken'];

        return self::generateAuthToken($authToken['login'], $expiration, $credential->getPassword(),
                                       self::AUTH_TOKEN_TYPE_BASIC);

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
     * @param ActionContext $actionContext
     * @param bool          $hydrateArray
     * @param RelativeField[]     $keyPaths
     * @param Filter[]      $filters
     * @param array         $params
     * @param string[]      $rights
     */
    public function findCredential (ActionContext $actionContext, $hydrateArray = true, array $keyPaths = [],
                                    array $filters = [],
                                    array $params = [],
                                    array $rights = []) {

        $qryCtx = new FindQueryContext('Credential', $actionContext->getRequestContext(), $rights);

        $actionContext['credentials'] = $this->basicFind($qryCtx, $hydrateArray, $keyPaths, $filters, $params);

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