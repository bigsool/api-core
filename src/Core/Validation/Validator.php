<?php


namespace Core\Validation;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Error\Error;
use Core\Parameter\UnsafeParameter;
use Core\Validation\Parameter\Constraint;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Validator\Validation;

class Validator {

    /**
     * TODO : who change params in ActionContext now ?
     * @param Constraint[][] $constraintsList
     * @param array          $params
     * @param bool           $forceOptional
     *
     * @return Error[]
     */
    public static function validateParams (array $constraintsList, array $params, $forceOptional = false) {

        $errorManager = ApplicationContext::getInstance()->getErrorManager();
        $errors = [];

        foreach ($constraintsList as $field => $constraints) {

            // if it's force optional and field not given, skip the validation of this field
            if ($forceOptional && !array_key_exists($field, $params)) {
                continue;
            }

            $value = isset($params[$field]) ? $params[$field] : NULL;

            $validator = Validation::createValidator();
            foreach ($constraints as $constraint) {
                // TODO : check if we can validate several constraint at the same time
                $violations = $validator->validate($value, [$constraint]);
                if ($violations->count()) {
                    $errors[] = $errorManager->getError($constraint->getErrorCode(), $field);
                }
            }

        }

        return $errors;

    }

    /**
     * @param ActionContext $context
     * @param array         $constraints
     *
     * @throws \Core\Error\FormattedError
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