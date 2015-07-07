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

        $type = $authToken['type'];

        unset($authToken['type'], $authToken['login'], $authToken['expiration'], $authToken['hash']);

        return static::generateAuthToken($credential, $type, NULL, $authToken);

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
     * @param array      $additionalParams
     *
     * @return array
     * @throws ToResolveException
     */
    public static function generateAuthToken ($credential, $authTokenType = NULL, $expirationTimestamp = NULL,
                                              array $additionalParams = []) {

        if (is_null($credential)) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        if (!$authTokenType) {
            $authTokenType = self::AUTH_TOKEN_BASIC;
        }

        if (!$expirationTimestamp) {
            $expirationTimestamp =
                time() + intval(ApplicationContext::getInstance()->getConfigManager()
                                                  ->getConfig()['credential']['expirationAuthToken']);
        }

        return static::createToken($credential->getLogin(), $expirationTimestamp, $credential->getPassword(),
                                   $authTokenType, $additionalParams);

    }

    /**
     * @param string $login
     * @param string $expiration
     * @param string $hashedPassword
     * @param string $type
     * @param array  $additionalParams
     *
     * @return array
     */
    protected static function createToken ($login, $expiration, $hashedPassword, $type, array $additionalParams = []) {

        $token = [
            'login'      => $login,
            'expiration' => $expiration,
            'type'       => $type
        ];

        $token = array_merge($token, $additionalParams);

        $token['hash'] = self::generateHash($hashedPassword, $token);

        return $token;

    }

    /**
     * @param $hashedPassword
     * @param $token
     *
     * @return string
     */
    protected static function generateHash ($hashedPassword, $token) {

        ksort($token);

        return sha1(implode($token) . $hashedPassword);

    }

    /**
     * @param mixed $authToken
     *
     * @return Credential
     * @throws ToResolveException
     */
    public static function checkAuthToken ($authToken) {

        static::checkAuthTokenStructure($authToken);

        $login = $authToken['login'];
        $expiration = $authToken['expiration'];

        if ($expiration < time() || !($credential = CredentialHelper::credentialForLogin($login))) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        if (!static::isAuthTokenValid($authToken, $credential->getPassword())) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        return $credential;

    }

    /**
     * @param array  $authToken
     * @param string $hashedPassword
     *
     * @return bool
     */
    protected static function isAuthTokenValid (array $authToken, $hashedPassword) {

        if (!isset($authToken['hash'])) {
            return false;
        }

        $hash = $authToken['hash'];

        unset($authToken['hash']);

        return $hash == self::generateHash($hashedPassword, $authToken);

    }

}