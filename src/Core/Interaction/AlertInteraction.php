<?php
/**
 * Created by PhpStorm.
 * User: tom
 * Date: 02/07/15
 * Time: 14:10
 */

namespace Core\Interaction;


class AlertInteraction implements Interaction {

    const TYPE = "alert";

    /**
     * @var string
     */
    protected $topic = null;

    /**
     * @var string
     */
    protected $message = null;

    /**
     * @param string $topic
     * @param string $message
     */
    public function __construct($topic, $message) {
        $this->topic    = $topic;
        $this->message  = $message;
    }

    /**
     * @return string
     */
    public function getType() {
        return self::TYPE;
    }

    /**
     * @return string
     */
    public function getTopic () {

        return $this->topic;
    }

    /**
     * @param string $topic
     */
    public function setTopic ($topic) {

        $this->topic = $topic;
    }

    /**
     * @return string
     */
    public function getMessage () {

        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage ($message) {

        $this->message = $message;
    }




}