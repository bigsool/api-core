<?php


namespace Archiweb\Action;


use Archiweb\Context\ActionContext;
use Archiweb\Context\ApplicationContext;
use Archiweb\Context\RequestContext;
use Archiweb\Error\FormattedError;
use Archiweb\Parameter\SafeParameter;
use Archiweb\Validation\AbstractConstraintsProvider;
use Archiweb\Validation\ConstraintsProvider;

class SimpleAction implements Action {

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
        $this->process = \Closure::bind($process, $this);
        $this->minRights = (array)$minRights;
        $this->setParams($params);

    }

    /**
     * @param array $params
     */
    protected function setParams (array $params) {

        $this->params = [];
        foreach ($params as $field => $param) {
            $errorManager = ApplicationContext::getInstance()->getErrorManager();
            if (!is_array($param) || count($param) < 2 || !$errorManager->getDefinedError($param[0])
                || !($param[1] instanceof ConstraintsProvider)
            ) {
                throw new \RuntimeException('invalid param');
            }
            $this->params[$field] =
                ['error' => $param[0], 'validator' => $param[1], 'forceOptional' => isset($param[2]) && !!$param[2]];
        }
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

                throw ApplicationContext::getInstance()->getErrorManager()->getFormattedError(ERR_PERMISSION_DENIED);

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

        return call_user_func($this->process, $context);

    }

    /**
     * @param ActionContext $context
     *
     * @throws FormattedError
     */
    public function validate (ActionContext $context) {

        $errorManager = ApplicationContext::getInstance()->getErrorManager();
        foreach ($this->params as $field => $params) {
            /**
             * @var AbstractConstraintsProvider $validator
             */
            $validator = $params['validator'];
            $param = $context->getParam($field);
            $value = isset($param) ? $param->getValue() : NULL;
            $violations = $validator->validate($field, $value, $params['forceOptional']);
            if ($violations->count()) {
                $errorManager->addError($params['error'], $field);
            }
            else {
                $safeParameter = new SafeParameter($value);
                $context->setParam($field, $safeParameter);
                $context->setVerifiedParam($field, $safeParameter);
            }
        }
        $errors = $errorManager->getErrors();
        if (!empty($errors)) {
            throw $errorManager->getFormattedError();
        }

    }
}