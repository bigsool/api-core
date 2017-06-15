<?php
namespace Core\Interaction;


class VisibilityInteraction extends AlertInteraction {

    const TYPE = "visibility";

    /**
     * @var isDisplayed
     */
    protected $isDisplayed;

    public function __construct ($isDisplayed) {
        parent::__construct(null,null);
        $this->isDisplayed = $isDisplayed;
    }

    public function toArray () {
        return [
             'type' => $this->getType(),
             'message' => $this->getMessage(),
             'topic' => $this->getTopic(),
             'value' => $this->isDisplayed
            ];
    }

}