<?php
namespace Core\Interaction;


class VisibilityInteraction extends AlertInteraction {

    const TYPE = "visibility";

    public function toArray () {
        return [
             'type' => $this->getType(),
             'message' => $this->getMessage(),
             'topic' => $this->getTopic(),
             'value' => true
            ];
    }

}