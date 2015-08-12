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
     * @param Credential[] $credentials
     *
     * @return array
     */
    public static function renewAuthToken ($authToken, array $credentials) {

        static::checkAuthTokenStructure($authToken);

        $type = $authToken['type'];

        unset($authToken['type'], $authToken['login'], $authToken['end'], $authToken['hash']);

        return static::generateAuthToken($credentials, $type, NULL, $authToken);

    }

    /**
     * @param $authToken
     *
     * @throws ToResolveException
     */
    protected static function checkAuthTokenStructure ($authToken) {


        if (!is_array($authToken) || !isset($authToken['login']) || !isset($authToken['end'])
            || !isset($authToken['type'])
        ) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

    }

    /**
     * @param Credential[] $credentials
     * @param string       $authTokenType
     * @param int          $expirationTimestamp
     * @param array        $additionalParams
     *
     * @return array
     * @throws ToResolveException
     */
    public static function generateAuthToken (array $credentials, $authTokenType = NULL, $expirationTimestamp = NULL,
                                              array $additionalParams = []) {

        foreach ($credentials as $credential) {
            if (!is_object($credential)) {
                throw new ToResolveException(ERROR_PERMISSION_DENIED); // we may have a better error code
            }
        }

        if (!$authTokenType) {
            $authTokenType = self::AUTH_TOKEN_BASIC;
        }

        if (!$expirationTimestamp) {
            $expirationTimestamp =
                time() + intval(ApplicationContext::getInstance()->getConfigManager()
                                                  ->getConfig()['credential']['expirationAuthToken']);
        }

        $login =
            count($credentials) == 1
                ? $credentials[0]->getLogin() : $credentials[0]->getLogin() . '#' . $credentials[1]->getLogin();
        $password = count($credentials) == 1 ? $credentials[0]->getPassword() : $credentials[1]->getPassword();

        return static::createToken($login, $expirationTimestamp, $password,
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
            'login' => $login,
            'end'   => $expiration,
            'type'  => $type
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
     * @return Credential[]
     * @throws ToResolveException
     */
    public static function checkAuthToken ($authToken) {

        static::checkAuthTokenStructure($authToken);

        $logins = explode('#', $authToken['login']);

        $loginUsedToLogIn = isset($logins[1]) ? $logins[1] : $logins[0];
        $guestLogin = isset($logins[1]) ? $logins[0] : NULL;
        $expiration = $authToken['end'];
        $guestCredential = NULL;
        $credentialUsedToLogIn = NULL;

        if ($expiration < time()) {
            throw new ToResolveException(ERROR_AUTH_TOKEN_EXPIRED);
        }

        if( !($credentialUsedToLogIn = CredentialHelper::credentialForLogin($loginUsedToLogIn)) ) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        if ($guestLogin && !($guestCredential = CredentialHelper::credentialForLogin($guestLogin))) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        if (!static::isAuthTokenValid($authToken, $credentialUsedToLogIn->getPassword())) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        return $guestLogin ? [$guestCredential, $credentialUsedToLogIn] : [$credentialUsedToLogIn];

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