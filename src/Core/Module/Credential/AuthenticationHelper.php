<?php


namespace Core\Module\Credential;


use Core\Context\ApplicationContext;
use Core\Error\ToResolveException;
use Core\Model\Credential;

class AuthenticationHelper {

    const AUTH_TOKEN_BASIC = 'basic';

    const AUTH_TOKEN_RESET_PASSWORD = 'resetPassword';

    /**
     * @param mixed      $authToken
     * @param Credential $credential
     *
     * @return array
     */
    public static function renewAuthToken ($authToken, $credential) {

        static::checkAuthTokenStructure($authToken);

        return static::generateAuthToken($credential, $authToken['type']);

    }

    /**
     * @param $authToken
     *
     * @throws ToResolveException
     */
    protected static function checkAuthTokenStructure ($authToken) {


        if (!is_array($authToken) || !isset($authToken['login']) || !isset($authToken['expiration'])
            || !isset($authToken['type'])
        ) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

    }

    /**
     * @param Credential $credential
     * @param string     $authTokenType
     * @param int        $expirationTimestamp
     *
     * @return array
     * @throws ToResolveException
     */
    public static function generateAuthToken ($credential, $authTokenType = NULL, $expirationTimestamp = NULL) {

        if (is_null($credential)) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        if (!$authTokenType) {
            $authTokenType = self::AUTH_TOKEN_BASIC;
        }

        if (!$expirationTimestamp) {
            $expirationTimestamp =
                time() + intval(ApplicationContext::getInstance()->getConfigManager()
                                                  ->getConfig()['expirationAuthToken']);
        }

        return static::createToken($credential->getLogin(), $expirationTimestamp, $credential->getPassword(),
                                   $authTokenType);

    }

    /**
     * @param string $login
     * @param string $expiration
     * @param string $hashedPassword
     * @param string $type
     *
     * @return array
     */
    protected static function createToken ($login, $expiration, $hashedPassword, $type) {

        return [
            'hash'       => sha1($login . $expiration . $hashedPassword . $type),
            'login'      => $login,
            'expiration' => $expiration,
            'type'       => $type
        ];

    }

    /**
     * @param mixed            $authToken
     *
     * @return Credential
     * @throws ToResolveException
     */
    public static function checkAuthToken ($authToken) {

        static::checkAuthTokenStructure($authToken);

        $login = $authToken['login'];

        $expiration = $authToken['expiration'];
        $type = $authToken['type'];

        if ($expiration < time() || !($credential = CredentialHelper::credentialForLogin($login))) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        $authTokenGenerated = static::createToken($login, $expiration, $credential->getPassword(), $type);

        if ($authTokenGenerated != $authToken) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        return $credential;

    }

}