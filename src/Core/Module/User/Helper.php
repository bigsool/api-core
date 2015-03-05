<?php


namespace Core\Module\User;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Model\User;
use Core\Module\BasicHelper;
use Core\Registry;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createUser (ActionContext $actCtx, array $params) {

        $userClassName = $this->realModelClassName('User');
        $user = new $userClassName;

        /**
         * @var User $user
         */
        $user->setCreationDate(new \DateTime());

        $this->basicSave($user, $params);

        $actCtx['user'] = $user;

    }

    /**
     * @param ActionContext $actCtx
     * @param               $user
     * @param array         $params
     */
    public function updateUser (ActionContext $actCtx, $user, array $params) {

        if (!is_a($user, $this->realModelClassName('User'))) {
            throw new \RuntimeException('Unexpected type for $user');
        }

        $this->basicSave($user, $params);

        $actCtx['user'] = $user;

    }

} 