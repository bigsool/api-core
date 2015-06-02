<?php


namespace Core\Action;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\RequestContext;
use Core\Error\FormattedError;
use Core\Validation\Validator;

class SimpleAction extends Action {

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
     * @var string[]
     */
    protected $minRights;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param string          $module
     * @param string          $name
     * @param string|string[] $minRights
     * @param array           $params
     * @param callable        $process
     */
    public function __construct ($module, $name, $minRights, array $params, callable $process) {

        if (!is_string($module) || empty($module)) {
            throw new \RuntimeException('invalid module');
        }

        if (!is_string($name) || empty($module)) {
            throw new \RuntimeException('invalid name');
        }

        $this->module = $module;
        $this->name = $name;
        // TODO: IDE cannot detect that this = GenericAction
        $this->process = /*\Closure::bind(*/
            $process/*, $this)*/
        ;
        $this->minRights = (array)$minRights;
        $this->params = $params;

    }

    /**
     * @return array
     */
    public function getParams () {

        return $this->params;
    }

    /**
     * @param ActionContext $context
     *
     * @return bool
     * @throws FormattedError
     */
    public function authorize (ActionContext $context) {

        $reqCtx = $context->getParentContext();
        if ($reqCtx instanceof RequestContext) {

            if (!$reqCtx->getAuth()->hasRights($this->minRights)) {

                throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERROR_PERMISSION_DENIED);

            }

        }

        return true;

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
     * @throws FormattedError
     */
    public function process (ActionContext $context) {

        $this->authorize($context);
        $this->validate($context);

        return call_user_func($this->process, $context, $this);

    }

    /**
     * @param ActionContext $context
     *
     * @throws FormattedError
     */
    public function validate (ActionContext $context) {

        // TODO : check how we validation actions now ?
        Validator::validate($context, $this->params);

    }
}