<?php


namespace Core\Interaction;


abstract class AbstractInteraction implements Interaction {

    const TYPE = '';

    /**
     * @var string
     */
    protected $topic;

    /**
     * @var string
     */
    protected $message;

    /**
     * @param string $topic
     * @param string $message
     */
    public function __construct ($topic, $message) {

        $this->setTopic($topic);
        $this->setMessage($message);

    }

    /**
     * @return string
     */
    public function getType () {

        return static::TYPE;

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

    /**
     * @return array
     */
    public function toArray () {

        return ['type' => $this->getType(), 'topic' => $this->getTopic(), 'message' => $this->getMessage()];

    }

}