<?php

namespace Core\Action;

use Core\Context\ActionContext;
use Core\Context\ApplicationContext;

class ActionReference extends Action {

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $name;

    /**
     * @param string $module
     * @param string $name
     */
    public function __construct ($module, $name) {

        if (!is_string($module) || empty($module)) {
            throw new \RuntimeException('invalid module');
        }

        if (!is_string($name) || empty($module)) {
            throw new \RuntimeException('invalid name');
        }

        $this->module = $module;
        $this->name = $name;

    }

    /**
     * @param ActionContext $context
     */
    public function authorize (ActionContext $context) {

        $this->call('authorize', $context);
    }

    /**
     * @return string
     */
    public function getModule () {

        return $this->module;
    }

    /**
     * @return string
     */
    public function getName () {

        return $this->name;
    }

    /**
     * @param ActionContext $context
     *
     * @return mixed
     */
    public function process (ActionContext $context) {

        return $this->call('process', $context);
    }

    /**
     * @param ActionContext $context
     */
    public function validate (ActionContext $context) {

        $this->call('validate', $context);
    }

    /**
     * @param string        $fn
     * @param ActionContext $context
     *
     * @return mixed
     */
    private function call ($fn, ActionContext $context) {

        foreach (ApplicationContext::getInstance()->getActions() as $action) {
            if ($action->getModule() == $this->getModule() && $action->getName() == $this->getName()) {
                return $action->$fn($context);
            }
        }
        throw new \RuntimeException('Action is not added to the application context yet !');
    }
}
