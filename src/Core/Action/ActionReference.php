<?php

namespace Core\Action;

use Core\Context\ActionContext;
use Core\Context\ApplicationContext;

class ActionReference implements Action {

    private $appCtx;

    function __construct (ApplicationContext $appCtx, $module, $name) {

        $this->appCtx = $appCtx;
        $this->module = $module;
        $this->name = $name;

    }


    private function call ($fn,$context) {

        foreach ($this->appCtx->getActions() as $action) {
            if ($action->getModule() == $this->getModule() && $action->getName() == $this->getName()) {
                return $action->$fn($context);
            }
        }
        throw new \Exception('Action is not added to the application context yet !');
    }

    /**
     * @param ActionContext $context
     */
    public function process (ActionContext $context) {
        return $this->call('process',$context);
    }

    /**
     * @param ActionContext $context
     */
    public function validate (ActionContext $context) {
        return $this->call('validate',$context);
    }

    /**
     * @param ActionContext $context
     */
    public function authorize (ActionContext $context) {
        return $this->call('authorize',$context);
    }

    /**
     * @return string
     */
    public function getName () {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getModule () {
        return $this->module;
    }
}
