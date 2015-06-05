<?php

namespace Core\Module\TestStorage;

use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Helper\GenericModuleEntityHelper;

class ModuleEntityHelper extends GenericModuleEntityHelper {

    /**
     * @param ApplicationContext $applicationContext
     */
    public function __construct (ApplicationContext $applicationContext) {

        parent::__construct($applicationContext, 'TestStorage');

    }

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     *
     * @return mixed
     */
    public function create (ActionContext $actCtx, array $params) {

        if (!isset($params['usedSpace'])) {
            $params['usedSpace'] = '0';
        }
        if (!isset($params['lastUsedSpaceUpdate'])) {
            $params['lastUsedSpaceUpdate'] = new \DateTime();
        }
        if (!isset($params['isOutOfQuota'])) {
            $params['isOutOfQuota'] = false;
        }

        return parent::create($actCtx, $params);

    }
}