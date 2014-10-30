<?php


namespace Archiweb\Module\UserFeature;


use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Model\User;
use Archiweb\Parameter\Parameter;
use Archiweb\Parameter\SafeParameter;

class Helper {

    /**
     * @param ActionContext $actCtx
     * @param Parameter[]   $params
     */
    public function createUser (ActionContext $actCtx, array $params) {

        $registry = ApplicationContext::getInstance()->getNewRegistry();

        $salt = self::createSalt();
        $params['password'] = new SafeParameter(self::encryptPassword($salt, $params['password']->getValue()));

        $user = new User();

        $user->setEmail($params['email']);
        $user->setPassword($params['password']);
        $user->setName($params['name']);
        $user->setFirstname($params['firstname']);
        $user->setLang($params['lang']);
        $user->setKnowsFrom($params['knowsFrom']);
        $user->setRegisterDate(new \DateTime());
        $user->setConfirmationKey(uniqid());
        $user->setSalt($salt);

        $registry->save($user);

        $actCtx['user'] = $user;

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