<?php


namespace Core\Module\User;


use Core\Context\ActionContext;
use Core\Model\User;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createUser (ActionContext $actCtx, array $params) {

        $user = $this->createRealModel('User');

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

        $this->checkRealModelType($user, 'User');

        $this->basicSave($user, $params);

        $actCtx['user'] = $user;

    }

} 