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

    const TYPE = 'plan_change';

    /**
     * @var array
     */
    protected $subscriptionParams = [];

    /**
     * @var array
     */
    protected $uiParams = [];

    /**
     * @return string
     */
    public function getType () {

        return self::TYPE;

    }

    /**
     * @return array
     */
    public function getSubscriptionParams () {

        return $this->subscriptionParams;

    }

    /**
     * @param array $subscriptionParams
     */
    public function setSubscriptionParams (array $subscriptionParams) {

        $this->subscriptionParams = $subscriptionParams;

    }

    /**
     * @return array
     */
    public function getUiParams () {

        return $this->uiParams;

    }

    /**
     * @param array $uiParams
     */
    public function setUiParams (array $uiParams) {

        $this->uiParams = $uiParams;

    }

    /**
     * @return array
     */
    public function toArray () {

        return array_merge([
            'type'                    => $this->getType(),
            'topic'                   => $this->getTopic(),
            'message'                 => $this->getMessage(),
            'subscription_definition' => $this->getSubscriptionParams(),
        ], $this->getUiParams());

    }

}