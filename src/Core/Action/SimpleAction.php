<?php


namespace Core\Action;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\RequestContext;
use Core\Error\FormattedError;
use Core\Error\ValidationException;
use Core\Util\ArrayExtra;
use Core\Validation\ConstraintsProvider;
use Core\Validation\Parameter\Constraint;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\NotNull;
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
    protected $constraintsList;

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
        $this->constraintsList = $params;

    }

    /**
     * @return array
     */
    public function getConstraintsList () {

        return $this->constraintsList;
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
     * @throws ValidationException
     */
    public function validate (ActionContext $context) {

        $errors = [];
        foreach ($this->constraintsList as $originalField => $constraints) {

            $explodedField = explode('.', $originalField);
            $field = end($explodedField);

            if (!is_array($constraints) || count($constraints) < 1) {
                throw new \RuntimeException('invalid constraints');
            }

            // handle constraintsProvider case
            if ($constraints[0] instanceof ConstraintsProvider) {
                $constraintsFor = $constraints[0]->getConstraintsFor($field);

                // handle forceOptional
                if (isset($constraints[1]) && $constraints[1]) {
                    $constraintsFor =
                        array_reduce($constraintsFor ?: [], function ($constraints, Constraint $constraint) {

                            if (!($constraint instanceof NotBlank || $constraint instanceof NotNull)) {
                            $constraints[] = $constraint;
                        }

                        return $constraints;

                    }, []);
                }
            }
            // handle constraint[] case
            else {
                $constraintsFor = [];
                foreach ($constraints as $constraint) {
                    if (!($constraint instanceof Constraint)) {
                        throw new \RuntimeException(sprintf('invalid constraints for %s', $originalField));
                    }
                    $constraintsFor[] = $constraint;
                }
            }

            $finalValue = $context->getFinalParam($originalField);
            $validationResult = Validator::validateValue($finalValue,$constraintsFor?:[], $originalField);
            if ($validationResult->hasErrors()) {
                $errors = array_merge($errors, $validationResult->getErrors());
            } elseif ($context->doesParamExist($originalField)) {
                $context->setParam($originalField, $finalValue);
            }
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

    }
}