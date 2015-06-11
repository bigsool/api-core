<?php


namespace Core\Action;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Error\FormattedError;
use Core\Parameter\UnsafeParameter;
use Core\Validation\AbstractConstraintsProvider;
use Core\Validation\RuntimeConstraintsProvider;

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

}