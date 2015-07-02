<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 02/07/15
 * Time: 14:18
 */

namespace Core\Module\Subscription;


use Core\Interaction\AlertInteraction;

class ChangeSubscriptionPlanInteraction extends AlertInteraction {

    const TYPE = 'change_subscription_plan';

    protected $subscriptionParams;

    protected $uiParams;

    public function getType() {
        return self::TYPE;
    }

    /**
     * @return mixed
     */
    public function getSubscriptionParams () {

        return $this->subscriptionParams;
    }

    /**
     * @param mixed $subscriptionParams
     */
    public function setSubscriptionParams ($subscriptionParams) {

        $this->subscriptionParams = $subscriptionParams;
    }

    /**
     * @return mixed
     */
    public function getUiParams () {

        return $this->uiParams;
    }

    /**
     * @param mixed $uiParams
     */
    public function setUiParams ($uiParams) {

        $this->uiParams = $uiParams;
    }



}