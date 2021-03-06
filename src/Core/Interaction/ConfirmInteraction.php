<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 02/07/15
 * Time: 14:14
 */

namespace Core\Interaction;


class ConfirmInteraction extends AlertInteraction {

    const TYPE = 'confirm';

    /**
     * @var string
     */
    protected $confirmAction = NULL;

    /**
     * @var string
     */
    protected $cancelAction = NULL;

    /**
     * @param string $topic
     * @param string $message
     * @param string $confirmAction
     * @param string $cancelAction
     */
    public function __construct ($topic, $message, $confirmAction = NULL, $cancelAction = NULL) {

        parent::__construct($topic, $message);

        $this->setConfirmAction($confirmAction);
        $this->setCancelAction($cancelAction);

    }

    /**
     * @return string
     */
    public function getCancelAction () {

        return $this->cancelAction;
    }

    /**
     * @param string $cancelAction
     */
    public function setCancelAction ($cancelAction) {

        $this->cancelAction = $cancelAction;
    }

    /**
     * @return string
     */
    public function getConfirmAction () {

        return $this->confirmAction;
    }

    /**
     * @param string $confirmAction
     */
    public function setConfirmAction ($confirmAction) {

        $this->confirmAction = $confirmAction;
    }

    /**
     * @return array
     */
    public function toArray () {

        return [
            'type'    => $this->getType(),
            'topic'   => $this->getTopic(),
            'message' => $this->getMessage(),
            'action'  => $this->getConfirmAction(),
            'cancel'  => $this->getCancelAction()
        ];

    }

}