<?php

namespace Core\Action;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;

class BasicFindAction extends SimpleAction {

    public function __construct ($module, $model, $helperName, $minRights, array $params,
                                 callable $preFindCallable = NULL, callable $postFindCallable = NULL) {

        if (!$preFindCallable) {
            $preFindCallable = function () {
            };
        }

        if (!$postFindCallable) {
            $postFindCallable = function () {
            };
        }

        parent::__construct($module, 'find', $minRights, $params,
            function (ActionContext $context) use (&$model, &$helperName, &$preFindCallable, &$postFindCallable) {

                $preFindCallable($context);

                $helper = ApplicationContext::getInstance()->getHelper($this, $helperName);
                $reqCtx = $context->getRequestContext();
                $method = 'find' . ucfirst($model);
                if (!is_callable([$helper, $method], false, $callableName)) {
                    throw new \RuntimeException($callableName . ' is not callable');
                }
                $helper->$method($context, $reqCtx->getReturnedFields(),
                                 $reqCtx->getFilter() ? [$reqCtx->getFilter()] : []);

                $postFindCallable($context);

                // TODO: pluralise $model

                return $context[$model];

            });

    }

}