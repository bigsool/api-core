<?php


namespace Core\Module\Credential;


use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Error\ToResolveException;
use Core\Helper\GenericModuleEntityHelper;
use Core\Model\Credential;

class CredentialHelper {

    /**
     * @param string $password
     *
     * @return string
     */
    public static function encryptPassword ($password) {

        return $password;

    }

    /**
     * @param array $authParams
     *
     * @return Credential[]
     * @throws ToResolveException
     * @throws \Exception
     */
    public static function credentialsForAuthParams (array $authParams) {

        if (!isset($authParams['authType']) || $authParams['authType'] != 'password') {
            throw new \Exception('unknown auth type');
        }

        $logins = explode('#', isset($authParams['login']) ? $authParams['login'] : '');
        $superLogin = isset($logins[1]) ? $logins[1] : $logins[0];
        $login = isset($logins[1]) ? $logins[0] : NULL;
        $timestamp = isset($authParams['timestamp']) ? $authParams['timestamp'] : 0;

        // if timestamp is not enough close, refuse the authentication
        if ($timestamp > (time() + 30) || $timestamp < (time() - 30)) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED);
        }

        $superUserCredential = static::credentialForLogin($superLogin);
        $superHash = sha1($superLogin . $superUserCredential->getPassword() . $timestamp);
        if ($superHash != (isset($authParams['hash']) ? $authParams['hash'] : '')) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED);
        }

        $credential = NULL;
        if ($login) {
            $credential = static::credentialForLogin($login);
        }

        return isset($login) ? [$credential, $superUserCredential] : [$superUserCredential];
    }

    /**
     * @param string $login
     *
     * @return Credential
     */
    public static function credentialForLogin ($login) {

        $qryCtx = new FindQueryContext('Credential', RequestContext::createNewInternalRequestContext());
        $qryCtx->addField('*');
        $qryCtx->addFilter('CredentialForLogin', $login);

        $credential = $qryCtx->findOne(ERROR_USER_NOT_FOUND);

        return $credential;

    }

    /**
     * @param int $id
     *
     * @return Credential
     */
    public static function credentialForId ($id) {

        $qryCtx = new FindQueryContext('Credential', RequestContext::createNewInternalRequestContext());
        $qryCtx->addField('*');
        $qryCtx->addFilter('CredentialForId', $id);

        $credential = $qryCtx->findOne(ERROR_PERMISSION_DENIED);

        return $credential;

    }

}