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

        return password_hash($password, PASSWORD_BCRYPT);

    }

    /**
     * @param array $authParams
     *
     * @return Credential[]
     * @throws ToResolveException
     * @throws \Exception
     */
    public static function credentialsForAuthParams (array $authParams) {

        if ($authParams['authType'] != 'password') {
            throw new \Exception('unknown auth type');
        }

        $logins = explode('#', $authParams['login']);
        $superLogin = isset($logins[1]) ? $logins[1] : $logins[0];
        $login = isset($logins[1]) ? $logins[0] : NULL;

        $superUserCredential = static::credentialForLogin($superLogin);
        if (!password_verify($authParams['password'], $superUserCredential->getPassword())) {
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