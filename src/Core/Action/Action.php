<?php


namespace Core\Action;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Error\FormattedError;
use Core\Parameter\UnsafeParameter;
use Core\Validation\AbstractConstraintsProvider;

abstract class Action {

    /**
     * @param ActionContext $context
     */
    public abstract function process (ActionContext $context);

    /**
     * @param ActionContext $context
     */
    public abstract function validate (ActionContext $context);

    /**
     * @param ActionContext $context
     */
    public abstract function authorize (ActionContext $context);

    /**
     * @return string
     */
    public abstract function getName ();

    /**
     * @return string
     */
    public abstract function getModule ();

    /**
     * @param ActionContext $context
     * @param array         $fields
     *
     * @throws FormattedError
     */
    public function validateParams (ActionContext $context, array $fields) {

        $_fields = [];
        foreach ($fields as $field => $param) {
            if (!is_array($param) || count($param) < 1 || !($param[0] instanceof AbstractConstraintsProvider)) {
                throw new \RuntimeException('invalid param');
            }
            $_fields[$field] = ['validator' => $param[0], 'forceOptional' => isset($param[1]) && !!$param[1]];
        }

        $errorManager = ApplicationContext::getInstance()->getErrorManager();
        foreach ($_fields as $field => $params) {
            /**
             * @var AbstractConstraintsProvider $validator
             */
            $validator = $params['validator'];
            $param = $context->getParam($field);
            $value = isset($param) ? UnsafeParameter::getFinalValue($param) : NULL;
            $path = isset($param) && $param instanceof UnsafeParameter ? $param->getPath() : $field;
            $isValid = $validator->validate($field, $value, $path, $params['forceOptional']);
            if ($isValid) {
                $context->setParam($field, $value);
            }
        }
        $errors = $errorManager->getErrors();
        if (!empty($errors)) {
            throw $errorManager->getFormattedError();
        }

    }

}