<?php


namespace Core\Module\Credential;

use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Field\KeyPath;
use Core\Filter\StringFilter;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function login (ActionContext $actionContext, array $params) {

        $registry = ApplicationContext::getInstance()->getNewRegistry();

        $qryCtx = new FindQueryContext('TestCredential');

        $qryCtx->addKeyPath(new KeyPath('*'));

        $qryCtx->addFilter(new StringFilter('TestCredential','bla','login = :login'));

        $qryCtx->setParams(['login' => $params['login']]);

        $user = $registry->find($qryCtx, true);
        $user = $user[0];

        $salt = $user['salt'];
        $paramsPassword = self::encryptPassword($salt,$params['password']);
        if ($paramsPassword != $user['password']) {
            throw new \RuntimeException('invalid credential');
        }


        if (!isset($params['authToken'])) { //TODO//
            $authTokenValidity = 1*60*60;
            $authToken = time() + $authTokenValidity;
        }
        else if ($params['authToken'] < time()) {
            throw new \RuntimeException('auth token expired');
        }


        return $authToken;

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

} 