<?php

namespace Core\Module\TestStorage;

use Core\Context\ActionContext;
use Core\Model\TestStorage;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createTestStorage (ActionContext $actCtx, array $params) {

        $storage = new TestStorage();

        if (!isset($params['usedSpace'])) {
            $params['usedSpace'] = '0';
        }
        if (!isset($params['lastUsedSpaceUpdate'])) {
            $params['lastUsedSpaceUpdate'] = new \DateTime();
        }
        if (!isset($params['isOutOfQuota'])) {
            $params['isOutOfQuota'] = false;
        }

        $this->basicSave($storage, $params);

        $actCtx['testStorage'] = $storage;

    }

    /**
     * @param ActionContext $actCtx
     * @param TestStorage   $storage
     * @param array         $params
     */
    public function updateTestStorage (ActionContext $actCtx, TestStorage $storage, array $params) {

        $this->basicSave($storage, $params);

        $actCtx['testStorage'] = $storage;

    }
}