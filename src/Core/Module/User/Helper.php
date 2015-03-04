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

        $user = new User();

        $user->setCreationDate(new \DateTime());

        $this->basicSave($user, $params);

        $actCtx['user'] = $user;

    }

    public function updateUser (ActionContext $actCtx, User $user, array $params) {

        $this->basicSave($user, $params);

        $actCtx['user'] = $user;

    }

} 