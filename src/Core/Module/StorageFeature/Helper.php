<?php

namespace Core\Module\StorageFeature;

use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Model\Storage;
use Core\Parameter\Parameter;

class Helper {

    /**
     * @param ActionContext $actCtx
     * @param Parameter[]   $params
     */
    public function createStorage (ActionContext $actCtx, array $params) {

        $registry = ApplicationContext::getInstance()->getNewRegistry();

        // TODO: change this to use the parameters
        $storage = new Storage();
        $storage->setUrl(uniqid('url'));
        $storage->setLogin(uniqid('login'));
        $storage->setPassword(uniqid('password'));
        $storage->setUsedSpace(0);
        $storage->setLastUsedSpaceUpdate(new \DateTime());
        $storage->setIsOutOfQuota(false);

        $registry->save($storage);

        $actCtx['storage'] = $storage;

    }

}