<?php


namespace Core\Module\Marketing;


use Core\Context\ActionContext;
use Core\Module\BasicHelper;

class Helper extends BasicHelper {

    public function createMarketingInfo (ActionContext $actCtx, array $params) {

        $marketingInfo = $this->createRealModel('MarketingInfo');

        $this->basicSave($marketingInfo, $params);

        $actCtx['marketingInfo'] = $marketingInfo;

    }

}