<?php

namespace Core\Module\Test\Storage;

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

        $params['usedSpace'] = '0';
        $params['lastUsedSpaceUpdate'] = new \DateTime();
        $params['isOutOfQuota'] = false;

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