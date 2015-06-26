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

    public static function credentialForAuthParams ($authParams) {

        if ( $authParams['authType'] != 'password' ) {
            throw new \Exception('unknown auth type');
        }

        $credential = static::credentialForLogin($authParams['login']);
        if (!password_verify($authParams['password'], $credential->getPassword())) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED);
        }

        return $credential;
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