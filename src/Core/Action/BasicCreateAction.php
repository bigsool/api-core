<?php

namespace Core\Action;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;

class BasicCreateAction extends SimpleAction {

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

        parent::__construct($module, 'create', $minRights, $params,
            function (ActionContext $context) use (&$model, &$helperName, &$preUpdateCallable, &$postUpdateCallable) {

                $preUpdateCallable($context);

                $helper = ApplicationContext::getInstance()->getHelper($helperName);
                $params = $context->getVerifiedParams();
                $method = 'create' . ucfirst($model);
                if (!is_callable([$helper, $method], false, $callableName)) {
                    throw new \RuntimeException($callableName . ' is not callable');
                }
                $helper->$method($context, $params);

                $postUpdateCallable($context);

                return $context[$model];

            });

    }

}