<?php


namespace Core\Module\TestUser;

use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Helper\GenericHelper;
use Core\Model\TestUser;

class Helper extends GenericHelper {

    /**
     * @param ApplicationContext $applicationContext
     */
    public function __construct (ApplicationContext $applicationContext) {

        parent::__construct($applicationContext, 'TestUser');

    }

    /**
     * @param ActionContext $actionContext
     * @param array         $params
     *
     * @return mixed
     */
    public function create (ActionContext $actionContext, array $params) {

        /**
         * @var TestUser $user
         */
        $user = parent::create($actionContext, $params);

        $user->setSalt(self::createSalt());
        $user->setPassword(self::encryptPassword($params['password']));
        $user->setRegisterDate(new \DateTime());
        $user->setConfirmationKey(uniqid());

        return $user;

    }

    /**
     * @return string
     */
    public static function createSalt () {

        return uniqid('', true);

    }

    /**
     * @param string $password
     *
     * @return string
     */
    public static function encryptPassword ($password) {

        return password_hash($password, PASSWORD_BCRYPT);

    }

    /**
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public static function verifyPassword($password, $hash) {

        return password_verify($password, $hash);

    }

} 