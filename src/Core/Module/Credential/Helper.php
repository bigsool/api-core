<?php


namespace Core\Module\Credential;

use Core\Auth;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Field\KeyPath;
use Core\Filter\Filter;
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

        $registry = $appCtx->getNewRegistry();

        $qryCtx = new FindQueryContext('Credential', new RequestContext(), [Auth::INTERNAL]);

        $qryCtx->addKeyPath(new KeyPath('*'));

        $qryCtx->addFilter($appCtx->getFilterByEntityAndName('Credential', 'filterByLogin'));

        $qryCtx->setParams(['login' => $params['login']]);

        $credential = $registry->find($qryCtx, false);

        if (count($credential) != 1) {
            throw $appCtx->getErrorManager()->getFormattedError(ERROR_USER_NOT_FOUND);
        }

        $credential = $credential[0];

        if (!password_verify($params['password'],$credential->getPassword())) {
            throw $appCtx->getErrorManager()->getFormattedError(ERROR_PERMISSION_DENIED);
        }

        $configManager = $appCtx->getConfigManager();
        $config = $configManager->getConfig();
        $expiration = time() + 10 * 60; //TODO//

        $this->createLoginHistory($actionContext, $credential, $params['loginHistory']);

        return self::generateAuthToken($params['login'], $expiration, $credential->getPassword(), self::AUTH_TOKEN_TYPE_BASIC);

    }


    /**
     * @param ActionContext $actionContext
     * @param               $credential
     *
     */
    protected function createLoginHistory (ActionContext $actionContext, $credential, $params) {

        $this->checkRealModelType($credential, 'Credential');

        $reqCtx = $actionContext->getRequestContext();

        $loginHistory = $this->createRealModel('LoginHistory');

        $params['credential']    = $credential;
        $params['date']          =  new \DateTime();
        $params['clientName']    = $reqCtx->getClientName();
        $params['clientVersion'] = $reqCtx->getClientVersion();
        $params['IP']            = $reqCtx->getIpAddress();

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
    public static function generateAuthToken ($login, $expiration, $hashedPassword, $type) {

        return [sha1($login . $expiration . $hashedPassword . $type), $login, $expiration, $type];

    }

    /**
     * @param ActionContext $actionContext
     * @param array         $params
     *
     */
    public function createCredential (ActionContext $actionContext, array $params) {

        $credential = $this->createRealModel('Credential');

        $params['password'] = password_hash($params['password'],PASSWORD_BCRYPT);

        $this->basicSave($credential, $params);

        $actionContext['credential'] = $credential;

    }

    /**
     * @param ActionContext $actionContext
     * @param bool          $hydrateArray
     * @param KeyPath[]     $keyPaths
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


} 