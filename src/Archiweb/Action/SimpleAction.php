<?php


namespace Archiweb\Action;


use Archiweb\Context\ActionContext;
use Archiweb\Error\ErrorManager;
use Archiweb\Error\FormattedError;
use Archiweb\Parameter\SafeParameter;
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
     * @var
     */
    protected $process;

    /**
     * @var
     */
    protected $minAuth;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param string   $module
     * @param string   $name
     * @param          $minAuth
     * @param array    $params
     * @param callable $process
     */
    public function __construct ($module, $name, $minAuth, array $params, callable $process) {

        if (!is_string($module) || empty($module)) {
            throw new \RuntimeException('invalid module');
        }

        if (!is_string($name) || empty($module)) {
            throw new \RuntimeException('invalid name');
        }

        $this->module = $module;
        $this->name = $name;
        $this->process = \Closure::bind($process, $this);
        $this->minAuth = $minAuth;
        $this->setParams($params);

    }

    /**
     * @param array $params
     */
    protected function setParams (array $params) {

        $this->params = [];
        foreach ($params as $field => $param) {
            if (!is_array($param) || count($param) < 2 || !ErrorManager::getDefinedError($param[0])
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
     */
    public function authorize (ActionContext $context) {
        // TODO: Implement authorize() method.
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

        $errorManager = $context->getApplicationContext()->getErrorManager($context->getRequestContext());
        foreach ($this->params as $field => $params) {
            /**
             * @var ConstraintsProvider $validator
             */
            list($error, $validator, $optional) = $params;
            $param = $context->getParam($field);
            $value = isset($param) ? $param->getValue() : NULL;
            $violations = $validator->validate($field, $value, $optional);
            if ($violations->count()) {
                $errorManager->addError($error, $field);
            }
            else {
                $context->setParam($field, new SafeParameter($value));
            }
        }
        if (!empty($errorManager->getErrors())) {
            throw $errorManager->getFormattedError();
        }

    }
}