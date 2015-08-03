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
                ? $credentials[0]->getLogin() : $credentials[1]->getLogin() . '#' . $credentials[0]->getLogin();

        return static::createToken($login, $expirationTimestamp, $credentials[0]->getPassword(),
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
        $superLogin = $logins[0];
        $login = isset($logins[1]) ? $logins[1] : NULL;
        $expiration = $authToken['end'];
        $credential = NULL;
        $superUserCredential = NULL;

        if ($expiration < time() || !($superUserCredential = CredentialHelper::credentialForLogin($superLogin))) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        if ($login && !($credential = CredentialHelper::credentialForLogin($login))) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        if (!static::isAuthTokenValid($authToken, $superUserCredential->getPassword())) {
            throw new ToResolveException(ERROR_PERMISSION_DENIED); // we may have a better error code
        }

        return $login ? [$credential, $superUserCredential] : [$superUserCredential];

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