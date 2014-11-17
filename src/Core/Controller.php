<?php


namespace Core;


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
     * @param string $module
     * @param string $actionName
     */
    public function __construct ($module, $actionName) {

        $this->module = $module;
        $this->actionName = $actionName;

    }

    /**
     * @param ActionContext $context
     *
     * @return mixed
     */
    public function apply (ActionContext $context) {

        return ApplicationContext::getInstance()->getAction($this->module, $this->actionName)->process($context);

    }

} 