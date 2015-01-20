<?php


namespace Core\Module\UserFeature;


use Core\Context\ActionContext;
use Core\Model\User;
use Core\Module\BasicHelper;
use Core\Parameter\UnsafeParameter;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createUser (ActionContext $actCtx, array $params) {

        $user = new User();

        $salt = self::createSalt();
        $params['password'] = self::encryptPassword($salt, UnsafeParameter::getFinalValue($params['password']));
        $user->setRegisterDate(new \DateTime());
        $user->setConfirmationKey(uniqid());
        $user->setSalt($salt);

        $this->basicSave($user, $params);

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

    public function updateUser (ActionContext $actCtx, User $user, array $params) {

        $this->basicSave($user, $params);

        $actCtx['user'] = $user;

    }

} 