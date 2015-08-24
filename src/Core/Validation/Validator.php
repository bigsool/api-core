<?php


namespace Core\Validation;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Parameter\UnsafeParameter;
use Core\Validation\Parameter\Constraint;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\NotNull;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validation;

class Validator {

    /**
     * @param Constraint[][] $constraintsList
     * @param array          $params
     * @param bool           $forceOptional
     *
     * @return ValidationResult
     */
    public static function validateParams (array $constraintsList, array $params, $forceOptional = false) {

        $errors = [];
        $validatedParams = [];

        foreach ($constraintsList as $field => $constraints) {

            // if it's force optional and field not given, skip the validation of this field
            if ($forceOptional && !array_key_exists($field, $params)) {
                continue;
            }

            $fieldValidationResult = static::validateParam($params, $field, $constraints);
            if ($fieldValidationResult->hasErrors()) {
                $errors = array_merge($errors, $fieldValidationResult->getErrors());
            }
            else {
                $validatedParams[$field] = $fieldValidationResult->getValue();
            }

        }

        return new ValidationResult($validatedParams, $errors);

    }

    /**
     * @param array        $params
     * @param string       $field
     * @param Constraint[] $constraints
     * @param bool         $forceOptional
     *
     * @return FieldValidationResult
     */
    public static function validateParam ($params, $field, $constraints, $forceOptional = false) {

        $value = UnsafeParameter::findFinalValue($params, $field);

        return static::validateValue($value, $constraints, $field, $forceOptional);

    }

    /**
     * @param mixed        $value
     * @param Constraint[] $constraints
     * @param string       $field Used to specify in the error which field failed
     * @param bool         $forceOptional
     *
     * @return FieldValidationResult
     */
    public static function validateValue ($value, array $constraints, $field = NULL, $forceOptional = false) {

        $errorManager = ApplicationContext::getInstance()->getErrorManager();
        $errors = [];

        $validator = Validation::createValidator();
        foreach ($constraints as $constraint) {
            if ($forceOptional && ($constraint instanceof NotBlank || $constraint instanceof NotNull)) {
                continue;
            }
            // TODO : check if we can validate several constraint at the same time
            $violations = $validator->validate($value, [$constraint->getConstraint()]);
            if ($violations->count()) {
                $errors[] = $errorManager->getError($constraint->getErrorCode(), $field);
            }
        }

        return new FieldValidationResult($field, $value, $errors);

    }

    /**
     * TODO : remove this method
     *
     * @param ActionContext $context
     * @param array         $constraints
     *
     * @throws \Core\Error\FormattedError
     * @deprecated
     */
    public static function validate (ActionContext $context, array $constraints) {

        // check constraints definition
        $_fields = [];
        foreach ($constraints as $field => $param) {
            if (!is_array($param) || count($param) < 1) {
                throw new \RuntimeException('invalid param');
            }
            if (!($param[0] instanceof ConstraintsProvider) && !is_array($param[0])) {
                throw new \RuntimeException('invalid constraints provider');
            }
            if (is_array($param[0])) {
                $param[0] = new RuntimeConstraintsProvider([$field => $param[0]]);
            }
            $_fields[$field] = ['constraintsProvider' => $param[0], 'forceOptional' => isset($param[1]) && !!$param[1]];
        }

        // do validation
        $errorManager = $context->getApplicationContext()->getErrorManager();
        foreach ($_fields as $field => $params) {
            /**
             * @var ConstraintsProvider $constraintsProvider
             */
            $constraintsProvider = $params['constraintsProvider'];
            $param = $context->getParam($field);
            $value = $context->getFinalParam($field, NULL);
            $path = isset($param) && $param instanceof UnsafeParameter ? $param->getPath() : $field;

            $isValid = self::validateField($context, $path, $constraintsProvider, $params['forceOptional'], $value);

            if ($isValid && !is_null($param)) {
                $context->setParam($field, $value);
            }
        }
        $errors = $errorManager->getErrors();
        if (!empty($errors)) {
            throw $errorManager->getFormattedError();
        }

    }

    /**
     * @param ActionContext       $context
     * @param string              $path
     * @param ConstraintsProvider $constraintsProvider
     * @param bool                $forceOptional
     * @param mixed               $value
     *
     * @return bool
     * @deprecated
     */
    protected static function validateField (ActionContext $context, $path, ConstraintsProvider $constraintsProvider,
                                             $forceOptional, $value) {

        $explodedPath = explode('.', $path);
        $name = end($explodedPath);
        $constraints = $constraintsProvider->getConstraintsFor($name, $context->getFinalParams());
        if ($forceOptional && $constraints) {
            $constraints = array_reduce($constraints, function ($constraints, Constraint $constraint) {

                if (!($constraint->getConstraint() instanceof Constraints\NotBlank)
                    && !($constraint->getConstraint() instanceof Constraints\NotNull)
                ) {
                    $constraints[] = $constraint;
                }

                return $constraints;

            }, []);
        }

        $isValid = true;

        if ($constraints) {
            foreach ($constraints as $constraint) {
                if (!($constraint instanceof Constraint)) {
                    throw new \RuntimeException('wrong constraint type');
                }
                $validator = Validation::createValidator();
                $violations = $validator->validate($value, [$constraint->getConstraint()]);
                if ($violations->count()) {
                    $context->getApplicationContext()->getErrorManager()
                            ->addError($constraint->getErrorCode(), $path);
                    $isValid = false;
                }
            }

            return $isValid;

        }

        return $isValid;
    }

}