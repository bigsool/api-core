<?php


namespace Core\Action;


use Core\Context\ActionContext;
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

    public function logCall (ActionContext $context) {

        $traceLogger = $context->getApplicationContext()->getTraceLogger();
        $traceLogger->trace(sprintf('action %s/%s called with params %s', $context->getModule(),
                                    $context->getActionName(), var_export($context->getParams(), true)));

    }

}