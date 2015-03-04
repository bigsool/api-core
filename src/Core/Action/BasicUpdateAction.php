<?php

namespace Core\Action;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Filter\StringFilter;
use Core\Validation\Parameter\Int;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\RuntimeConstraintsProvider;

class BasicUpdateAction extends SimpleAction {

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

        $params =
            array_merge($params, ['id' => [new RuntimeConstraintsProvider(['id' => [new NotBlank(), new Int()]])]]);

        parent::__construct($module, 'update', $minRights, $params,
            function (ActionContext $context) use (&$model, &$helperName, &$preUpdateCallable, &$postUpdateCallable) {

                $preUpdateCallable($context);

                $params = $context->getVerifiedParams();

                $qryCtx = new FindQueryContext($model);
                $qryCtx->addKeyPath(new \Core\Field\KeyPath('*'));
                $qryCtx->addFilter(new StringFilter($model, '', 'id = :id'));
                $qryCtx->setParams(['id' => $params['id']]);

                $helper = ApplicationContext::getInstance()->getHelper($helperName);
                $entities = ApplicationContext::getInstance()->getNewRegistry()->find($qryCtx, false);

                if (count($entities) != 1) {
                    throw new \RuntimeException('more or less than one entity found');
                }

                $method = 'update' . ucfirst($model);
                if (!is_callable([$helper, $method], false, $callableName)) {
                    throw new \RuntimeException($callableName . ' is not callable');
                }

                unset($params['id']);

                $helper->$method($context, $entities[0], $params);

                $postUpdateCallable($context);

                return $context[lcfirst($model)];

            });

    }

}