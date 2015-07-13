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
    protected $subscriptionParams;

    /**
     * @var TODO
     */
    protected $uiParams;

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

    /**
     * @return array
     */
    public function toArray () {

        return [
            'type'                    => $this->getType(),
            'topic'                   => $this->getTopic(),
            'message'                 => $this->getMessage(),
            'subscription_definition' => $this->getSubscriptionParams(),
            'ui'                      => $this->getUiParams()
        ];

    }

}