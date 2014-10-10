<?php


namespace Archiweb;


use Archiweb\Action\Action;
use Archiweb\Context\ActionContext;

class Controller {

    /**
     * @var Action
     */
    protected $action;

    /**
     * @param Action $action
     */
    public function __construct (Action $action) {

        $this->action = $action;

    }

    /**
     * @param ActionContext $context
     *
     * @return mixed
     */
    public function apply (ActionContext $context) {

        return $this->action->process($context);

    }

} 