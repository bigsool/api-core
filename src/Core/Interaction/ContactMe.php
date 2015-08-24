<?php


namespace Core\Interaction;


class ContactMe extends AbstractInteraction {

    const TYPE = 'contact_me';

    /**
     * @var string
     */
    protected $template;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @return string
     */
    public function getTemplate () {

        return $this->template;

    }

    /**
     * @param string $template
     */
    public function setTemplate ($template) {

        $this->template = $template;

    }

    /**
     * @return array
     */
    public function getParams () {

        return $this->params;

    }

    /**
     * @param array $params
     */
    public function setParams (array $params) {

        $this->params = $params;

    }

    /**
     * @inheritDoc
     */
    public function toArray () {

        return [
            'type'     => $this->getType(),
            'topic'    => $this->getTopic(),
            'message'  => $this->getMessage(),
            'template' => $this->getTemplate(),
            'params'   => $this->getParams(),
        ];

    }

}