<?php


namespace Core\Module\User;


use Core\Context\ActionContext;
use Core\Context\FindQueryContext;
use Core\Field\RelativeField;
use Core\Filter\Filter;
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

    /**
     * @param ActionContext   $actionContext
     * @param RelativeField[] $keyPaths
     * @param Filter[]        $filters
     * @param array           $params
     */
    public function findUser (ActionContext $actionContext, array $keyPaths = [], array $filters = [],
                              array $params = []) {

        $qryCtx = new FindQueryContext('User', $actionContext->getRequestContext());

        $actionContext['users'] = $this->basicFind($qryCtx, $keyPaths, $filters, $params);

    }

} 