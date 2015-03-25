<?php


namespace Core\Module\Credential;

use Core\Auth;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Field\KeyPath;
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

        $registry = $appCtx->getNewRegistry();

        $qryCtx = new FindQueryContext('Credential', $actionContext->getRequestContext(), Auth::INTERNAL);

        $qryCtx->addKeyPath(new KeyPath('*'));

        $qryCtx->addFilter($appCtx->getFilterByEntityAndName('Credential', 'filterByLogin'));

        $qryCtx->setParams(['login' => $params['login']]);

        /**
         * @var \Core\Model\Credential[] $users
         */
        $users = $registry->find($qryCtx, false);

        if (count($users) != 1) {
            throw $appCtx->getErrorManager()->getFormattedError(ERROR_USER_NOT_FOUND);
        }

        $user = $users[0];

        $salt = $user->getSalt();
        $hashedPassword = self::encryptPassword($salt, $params['password']);
        if ($hashedPassword != $user->getPassword()) {
            throw $appCtx->getErrorManager()->getFormattedError(ERROR_PERMISSION_DENIED);
        }
        $configManager = $appCtx->getConfigManager();
        $config = $configManager->getConfig();
        $expiration = time() + 10 * 60; //TOTEST//

        $this->createLoginHistory($actionContext, $user);

        return self::generateAuthToken($params['login'], $expiration, $hashedPassword, $salt, self::AUTH_TOKEN_TYPE_BASIC);

    }

    /**
     * @param string $salt
     * @param string $password
     *
     * @return string
     */
    public static function encryptPassword ($salt, $password) {

        $hash = $salt . $password;
        for ($i = 0; $i < 3004; ++$i) {
            $hash = hash('sha512', $salt . $hash);
        }

        return $hash;

    }

    /**
     * @param string $login
     * @param string $expiration
     * @param string $hashedPassword
     * @param        $salt
     * @param string $type
     *
     * @return array
     */
    public static function generateAuthToken ($login, $expiration, $hashedPassword, $salt, $type) {

        return [sha1($login . $expiration . $hashedPassword . $salt . $type), $login, $expiration, $type];

    }

    /**
     * @param ActionContext $actionContext
     * @param array         $params
     *
     * @return mixed
     */
    public function createCredential (ActionContext $actionContext, array $params) {

        $credential = $this->createRealModel('Credential');

        $params['salt'] = Helper::createSalt();
        $params['password'] = Helper::encryptPassword($params['salt'], $params['password']);

        $this->basicSave($credential, $params);

        $actionContext['credential'] = $credential;

    }

    /**
     * @return string
     */
    public static function createSalt () {

        return uniqid('', true);

    }

    /**
     * @param ActionContext $actionContext
     * @param               $credential
     *
     * @return mixed
     */
    protected function createLoginHistory (ActionContext $actionContext, $credential) {

        $this->checkRealModelType($credential, 'Credential');

        $reqCtx = $actionContext->getRequestContext();

        $loginHistory = $this->createRealModel('LoginHistory');

        $this->basicSave($loginHistory, [
            'credential'    => $credential,
            'clientName'    => $reqCtx->getClientName(),
            'clientVersion' => $reqCtx->getClientVersion(),
            'IP'            => $reqCtx->getIpAddress(),
            'date'          => new \DateTime(),
        ]);

        $actionContext['loginHistory'] = $loginHistory;

    }


} 