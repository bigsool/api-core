<?php

namespace Core\Action;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;

class BasicCreateAction extends SimpleAction {

    /**
     * @param string   $module
     * @param string   $model
     * @param string   $helperName
     * @param array    $minRights
     * @param array    $params
     * @param callable $preCreateCallable
     * @param callable $postCreateCallable
     */
    public function __construct ($module, $model, $helperName, $minRights, array $params,
                                 callable $preCreateCallable = NULL, callable $postCreateCallable = NULL) {

        if (!$preCreateCallable) {
            $preCreateCallable = function () {
            };
        }

        if (!$postCreateCallable) {
            $postCreateCallable = function () {
            };
        }

        parent::__construct($module, 'create', $minRights, $params,
            function (ActionContext $context) use (&$model, &$helperName, &$preCreateCallable, &$postCreateCallable) {

                $preCreateCallable($context);

                $helper = ApplicationContext::getInstance()->getHelper($this, $helperName);
                $params = $context->getVerifiedParams();
                $method = 'create' . ucfirst($model);
                if (!is_callable([$helper, $method], false, $callableName)) {
                    throw new \RuntimeException($callableName . ' is not callable');
                }
                $helper->$method($context, $params);

                $postCreateCallable($context);

                return $context[lcfirst($model)];

            });

    }

}