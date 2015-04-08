<?php


namespace Core\Module\Marketing;


use Core\Context\ActionContext;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    /**
     * @param ActionContext $actCtx
     * @param array         $params
     */
    public function createMarketingInfo (ActionContext $actCtx, array $params) {

        $marketingInfo = $this->createRealModel('MarketingInfo');

        $this->basicSave($marketingInfo, $params);

        $actCtx['marketingInfo'] = $marketingInfo;

    }

    /**
     * @param ActionContext $actCtx
     * @param               $marketingInfo
     * @param array         $params
     */
    public function updateMarketingInfo (ActionContext $actCtx, $marketingInfo, array $params) {

        $this->checkRealModelType($marketingInfo, 'MarketingInfo');

        $this->basicSave($marketingInfo, $params);

        $actCtx['marketingInfo'] = $marketingInfo;

    }

}