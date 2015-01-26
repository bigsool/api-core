<?php


namespace Core;


use Core\Action\Action;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;

class Controller {

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $actionName;

    /**
     * @var Action
     */
    protected $action;

    /**
     * @param string|Action $action
     * @param string        $module
     */
    public function __construct ($action, $module = NULL) {

        $this->module = $module;
        if (is_string($action)) {
            if (is_null($module)) {
                throw new \RuntimeException('if action is a string, module must be provided');
            }
            $this->actionName = $action;
        }
        elseif ($action instanceof Action) {
            $this->action = $action;
        }
        else {
            throw new \RuntimeException('invalid action type');
        }

    }

    protected function getAction () {

        return isset($this->action)
            ? $this->action
            : ApplicationContext::getInstance()->getAction($this->module, $this->actionName);

    }

    /**
     * @param ActionContext $context
     *
     * @return mixed
     */
    public function apply (ActionContext $context) {

        return $this->getAction()->process($context);

    }

} 