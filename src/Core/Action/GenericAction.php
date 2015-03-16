<?php


namespace Core\Action;


use Closure;
use Core\Context\ActionContext;

class GenericAction extends Action {

    /**
     * @var string
     */
    protected $module;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var callable
     */
    protected $process;

    /**
     * @var callable
     */
    protected $validate;

    /**
     * @var callable
     */
    protected $authorize;

    /**
     * @param string   $module
     * @param string   $name
     * @param callable $authorize
     * @param callable $validate
     * @param callable $process
     */
    public function __construct ($module, $name, callable $authorize, callable $validate, callable $process) {

        if (!is_string($module) || empty($module)) {
            throw new \RuntimeException('invalid module');
        }

        if (!is_string($name) || empty($module)) {
            throw new \RuntimeException('invalid name');
        }

        $this->module = $module;
        $this->name = $name;
        // TODO: IDE cannot detect that this = GenericAction
        $this->process = /*Closure::bind(*/$process/*, $this)*/;
        $this->validate = /*Closure::bind(*/$validate/*, $this)*/;
        $this->authorize = /*Closure::bind(*/$authorize/*, $this)*/;

    }

    /**
     * @param ActionContext $context
     */
    public function authorize (ActionContext $context) {

        call_user_func($this->authorize, $context, $this);

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

        $this->authorize($context);

        $this->validate($context);

        return call_user_func($this->process, $context, $this);

    }

    /**
     * @param ActionContext $context
     */
    public function validate (ActionContext $context) {

        call_user_func($this->validate, $context, $this);

    }
}