<?php

namespace Core\Action;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;

class BasicFindAction extends SimpleAction {

    public function __construct ($module, $model, $helperName, $minRights, array $params,
                                 callable $preUpdateCallable = NULL, callable $postUpdateCallable = NULL) {

        if (!$preUpdateCallable) {
            $preUpdateCallable = function () {
            };
        }

        if (!$postUpdateCallable) {
            $postUpdateCallable = function () {
            };
        }

        parent::__construct($module, 'find', $minRights, $params,
            function (ActionContext $context) use (&$model, &$helperName, &$preUpdateCallable, &$postUpdateCallable) {

                $preUpdateCallable($context);

                $helper = ApplicationContext::getInstance()->getHelper($helperName);
                $reqCtx = $context->getRequestContext();
                $method = 'find' . ucfirst($model);
                if (!is_callable([$helper, $method], false, $callableName)) {
                    throw new \RuntimeException($callableName . ' is not callable');
                }
                $helper->$method($context, $reqCtx->getReturnedKeyPaths(), [$reqCtx->getFilter()]);

                $postUpdateCallable($context);

                return $context[$model];

            });

    }

}