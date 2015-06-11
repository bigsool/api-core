<?php


namespace Core\Action;


use Core\Context\ActionContext;
use Core\Context\RequestContext;
use Core\Error\FormattedError;
use Core\Error\ValidationException;
use Core\Validation\ConstraintsProvider;
use Core\Validation\Parameter\Constraint;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\NotNull;
use Core\Validation\Validator;

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
     * @var string[]
     */
    protected $minRights;

    /**
     * @param string                   $module
     * @param string                   $name
     * @param callable|string|string[] $authorize
     * @param callable|array           $validate
     * @param callable                 $process
     */
    public function __construct ($module, $name, $authorize, $validate, callable $process) {

        if (!is_string($module) || empty($module)) {
            throw new \InvalidArgumentException('invalid module');
        }

        if (!is_string($name) || empty($module)) {
            throw new \InvalidArgumentException('invalid name');
        }

        if (!is_callable($validate) && !is_array($validate)) {
            throw new \InvalidArgumentException('$validate must be a callable or an array');
        }

        if (!is_callable($authorize) && !is_array($authorize) && !is_string($authorize)) {
            throw new \InvalidArgumentException('$authorize must be a callable or a string or an array of string');
        }

        $this->module = $module;
        $this->name = $name;
        $this->process = $process;
        $this->validate = is_callable($validate)
            ? $validate
            : function (ActionContext $context) use ($validate) {

                static::simpleValidate($validate, $context);

            };
        $this->authorize = is_callable($authorize)
            ? $authorize
            : function (ActionContext $context) use ($authorize) {

                return static::simpleAuthorize($authorize, $context);

            };

    }

    /**
     * @param array         $constraintsList
     * @param ActionContext $context
     *
     * @throws ValidationException
     */
    public static function simpleValidate (array $constraintsList, ActionContext $context) {

        $errors = [];
        foreach ($constraintsList as $originalField => $constraints) {

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
            $validationResult = Validator::validateValue($finalValue, $constraintsFor ?: [], $originalField);
            if ($validationResult->hasErrors()) {
                $errors = array_merge($errors, $validationResult->getErrors());
            }
            elseif ($context->doesParamExist($originalField)) {
                $context->setParam($originalField, $finalValue);
            }
        }

        if ($errors) {
            throw new ValidationException($errors);
        }

    }

    /**
     * @param string|string[] $minRights
     * @param ActionContext   $context
     *
     * @return bool
     * @throws FormattedError
     */
    public static function simpleAuthorize ($minRights, ActionContext $context) {

        $reqCtx = $context->getParentContext();
        if ($reqCtx instanceof RequestContext) {

            if (!$reqCtx->getAuth()->hasRights($minRights)) {

                throw $context->getApplicationContext()->getErrorManager()->getFormattedError(ERROR_PERMISSION_DENIED);

            }

        }

        return true;

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