<?php


namespace Core\Module\Credential;

use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Field\KeyPath;
use Core\Filter\StringFilter;
use Core\Model\Credential;
use Core\Model\TestCredential;
use Core\Module\BasicHelper;
use Core\Parameter\UnsafeParameter;

class Helper extends BasicHelper {

    const AUTH_TOKEN_TYPE_BASIC = 'basic';

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function login (ActionContext $actionContext, array $params) {

        $registry = ApplicationContext::getInstance()->getNewRegistry();

        $qryCtx = new FindQueryContext('Credential');

        $qryCtx->addKeyPath(new KeyPath('*'));

        $qryCtx->addFilter(new StringFilter('Credential','filterbylogin','login = :login'));

        $login = UnsafeParameter::getFinalValue($params['login']);

        $qryCtx->setParams(['login' => $login]);

        $user = $registry->find($qryCtx, true);
        $user = $user[0];

        $salt = $user['salt'];
        $hashedPassword = self::encryptPassword($salt,$params['password']);
        if ($hashedPassword != $user['password']) {
            throw new \RuntimeException('invalid credential');
        }
        $appCtx = ApplicationContext::getInstance();
        $configManager = $appCtx->getConfigManager();
        $config = $configManager->getConfig();
        $expiration =  time() + 10 * 60; //TOTEST//

        return self::generateAuthToken($login,$expiration,$hashedPassword,self::AUTH_TOKEN_TYPE_BASIC);

    }

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function create (ActionContext $actionContext, array $params) {

        $registry = ApplicationContext::getInstance()->getNewRegistry();

        $credential = new TestCredential();
        $credential->setLogin(UnsafeParameter::getFinalValue($params['login']));

        $salt = Helper::createSalt();
        $password = Helper::encryptPassword($salt, UnsafeParameter::getFinalValue($params['password']));

        $credential->setPassword($password);
        $credential->setSalt($salt);

        $registry->save($credential);

        return $credential;

    }


    /**
     * @return string



     */
    public static function createSalt () {

        return uniqid('', true);

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
     * @param string $end
     * @param string $hashedPassword
     * @param string $type
     * @return array
     */
    public static function generateAuthToken ($login,$expiration,$hashedPassword,$type) {

        return [sha1($login.$expiration.$hashedPassword.$type),$login,$expiration,$type];

    }


} 