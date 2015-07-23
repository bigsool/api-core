<?php


namespace Core\Interaction;


class ContactMe extends AbstractInteraction {

    const TYPE = 'contact-me';

    /**
     * @inheritDoc
     */
    public function toArray () {

        return [
            'type' => $this->getType(),
            'topic' => $this->getTopic(),
            'message' => $this->getMessage(),
            // TODO
        ];

    }

}