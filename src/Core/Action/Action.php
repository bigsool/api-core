<?php


namespace Core\Action;


use Core\Context\ActionContext;

interface Action {

    /**
     * @param ActionContext $context
     */
    public function process (ActionContext $context);

    /**
     * @param ActionContext $context
     */
    public function validate (ActionContext $context);

    /**
     * @param ActionContext $context
     */
    public function authorize (ActionContext $context);

    /**
     * @return string
     */
    public function getName ();

    /**
     * @return string
     */
    public function getModule ();

} 