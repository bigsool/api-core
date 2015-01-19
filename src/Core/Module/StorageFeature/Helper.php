<?php

namespace Core\Module\StorageFeature;

use Core\Context\ActionContext;
use Core\Model\Storage;
use Core\Module\BasicHelper;
use Core\Parameter\Parameter;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actCtx
     * @param Parameter[]   $params
     */
    public function createStorage (ActionContext $actCtx, array $params) {

        $storage = new Storage();

        $params['usedSpace'] = '0';
        $params['lastUsedSpaceUpdate'] = new \DateTime();
        $params['isOutOfQuota'] = false;

        $this->basicSave($storage, $params);

        $actCtx['storage'] = $storage;

    }

    /**
     * @param ActionContext $actCtx
     * @param Storage       $storage
     * @param Parameter[]   $params
     */
    public function updateStorage (ActionContext $actCtx, Storage $storage, array $params) {

        $this->basicSave($storage, $params);

        $actCtx['storage'] = $storage;

    }
}