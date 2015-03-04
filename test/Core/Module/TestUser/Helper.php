<?php


namespace Core\Module\TestUser;

use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Model\TestUser;
use Core\Module\BasicHelper;
use Core\Parameter\UnsafeParameter;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createTestUser (ActionContext $actCtx, array $params) {

        $user = new TestUser();

        $salt = self::createSalt();
        $params['password'] = self::encryptPassword($salt, UnsafeParameter::getFinalValue($params['password']));
        $user->setRegisterDate(new \DateTime());
        $user->setConfirmationKey(uniqid());
        $user->setSalt($salt);

        $this->basicSave($user, $params);

        $actCtx['testUser'] = $user;

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
     * @param ActionContext $actCtx
     * @param TestUser      $user
     * @param array         $params
     */
    public function updateTestUser (ActionContext $actCtx, TestUser $user, array $params) {

        $this->basicSave($user, $params);

        $actCtx['testUser'] = $user;

    }

    /**
     * @param ActionContext $actCtx
     * @param KeyPath[]     $keyPaths
     * @param Filter[]      $filters
     * @param bool          $hydrateArray
     */
    public function findTestUser (ActionContext $actCtx, array $keyPaths = [], array $filters = [],
                                     $hydrateArray = true) {

        $registry = ApplicationContext::getInstance()->getNewRegistry();

        $qryCtx = new FindQueryContext('TestUser');

        foreach ($keyPaths as $keyPath) {
            $qryCtx->addKeyPath($keyPath);
        }
        foreach ($filters as $filter) {
            $qryCtx->addFilter($filter);
        }

        $actCtx['TestUser'] = $registry->find($qryCtx, $hydrateArray);

    }

} 