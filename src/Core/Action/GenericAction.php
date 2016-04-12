<?php


namespace Core\Action;


use Core\Context\ActionContext;
use Core\Context\RequestContext;
use Core\Error\FormattedError;
use Core\Util\ArrayExtra;
use Core\Validation\ConstraintsProvider;
use Core\Validation\Parameter\Constraint;
use Core\Validation\Parameter\Object;
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
            throw new \InvalidArgumentException(sprintf('$validate must be a callable or an array, %s given',
                                                        gettype($validate)));
        }

        if (!is_callable($authorize) && !is_array($authorize) && !is_string($authorize) && !is_null($authorize)) {
            throw new \InvalidArgumentException(sprintf('$authorize must be a callable or a string or an array of string, %s given',
                                                        gettype($authorize)));
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
     * @param null|string   $basePath If provided, basePath will be added in all fields: $base.$field
     *
     * @throws FormattedError
     */
    public static function simpleValidate (array $constraintsList, ActionContext $context, $basePath = NULL) {


        $errors = [];

        $blackListedFields = [];
        foreach ($constraintsList as $originalField => $constraints) {

            if (in_array($originalField, $blackListedFields)) {
                continue;
            }

            if ($basePath) {
                $originalField = $basePath . '.' . $originalField;
            }

            $explodedField = explode('.', $originalField);
            $field = end($explodedField);

            if (!is_array($constraints) || count($constraints) < 1) {
                throw new \RuntimeException('invalid constraints');
            }

            // in case we wanna validate an array of something
            if (ArrayExtra::isAssociative($constraints)) {
                static::simpleValidate([$originalField => [new Object()]], $context);
                $arrayToValidate = $context->getFinalParam($originalField, []);
                $shouldBeFlat = count($constraints) == 1 && isset($constraints['']);
                foreach ($arrayToValidate as $key => $valueToValidate) {
                    $subConstraintsList = [];
                    foreach ($constraints as $subField => $subConstraints) {
                        $subOriginalField = $originalField . '.' . $key . ($shouldBeFlat ? '' : '.' . $subField);
                        $subConstraintsList[$subOriginalField] = $subConstraints;
                    }
                    static::simpleValidate($subConstraintsList, $context);
                }
                continue;
            }

            $forceOptional = false;
            // handle constraintsProvider case
            if ($constraints[0] instanceof ConstraintsProvider) {
                $constraintsFor = $constraints[0]->getConstraintsFor($field);
                if (isset($constraints[1])) {
                    $forceOptional = $constraints[1];
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
            $finalParams = $context->getFinalParams();
            $explodedField = explode('.', $originalField);
            for ($i = 0; $i < count($explodedField) - 1; ++$i) {
                if (isset($finalParams[$explodedField[$i]])) {
                    $finalParams = $finalParams[$explodedField[$i]];
                }
                else {
                    break;
                }
            }

            $result =
                Validator::validateParams([$explodedField[count($explodedField) - 1] => $constraintsFor], $finalParams,
                                          $forceOptional);

            if ($result->hasErrors()) {

                $errors = array_merge($errors, $result->getErrors());

            }
            elseif ($context->doesParamExist($originalField)) {

                $context->setParam($originalField, $finalValue);

            }

            // Remove check which must not be done.
            // Use case : contact of a projectMember is not mandatory but if given, contact name is mandatory
            // So if contact is null we don't want to check contact fields
            if (!$result->hasErrors() && is_null($finalValue)) {
                $originalFieldPrefix = $originalField . '.';
                foreach (array_keys($constraintsList) as $constraintField) {
                    if (substr($constraintField, 0, strlen($originalFieldPrefix)) == $originalFieldPrefix) {
                        $blackListedFields[] = $constraintField;
                    }
                }
            }

        }

        if (count($errors) > 0) {

            $errMgr = $context->getApplicationContext()->getErrorManager();
            $errMgr->addErrors($errors);

            throw $errMgr->getFormattedError();

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

        $this->logCall($context);

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