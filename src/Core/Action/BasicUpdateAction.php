<?php

namespace Core\Action;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Field\RelativeField;
use Core\Filter\StringFilter;
use Core\Helper\BasicHelper;
use Core\Module\ModuleEntity;
use Core\Validation\Parameter\Integer;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\RuntimeConstraintsProvider;

class BasicUpdateAction extends GenericAction {

    /**
     * @param string           $module
     * @param ModuleEntity     $moduleEntity
     * @param string|\string[] $minRights
     * @param array            $params
     * @param callable         $preUpdateCallable
     * @param callable         $postUpdateCallable
     */
    public function __construct ($module, ModuleEntity $moduleEntity, $minRights, array $params,
                                 callable $preUpdateCallable = NULL,
                                 callable $postUpdateCallable = NULL) {

        if (!$preUpdateCallable) {
            $preUpdateCallable = function () {
            };
        }

        if (!$postUpdateCallable) {
            $postUpdateCallable = function () {
            };
        }

        $factory = ApplicationContext::getInstance()->getParameterFactory();

        $params =
            array_merge($params, ['id' => [new RuntimeConstraintsProvider(['id' => [$factory->getParameter(NotBlank::class), $factory->getParameter(Integer::class)]])]]);

        parent::__construct($module, 'update', $minRights, $params,
            function (ActionContext $context, BasicUpdateAction $action) use (
                &$moduleEntity, &$helperName, &$preUpdateCallable, &$postUpdateCallable
            ) {

                $preUpdateCallable($context, $action);

                $params = $context->getVerifiedParams();

                $reqCtx = $context->getRequestContext()->copyWithoutRequestedFields();

                $entityName = $moduleEntity->getDefinition();

                $moduleEntity = ucfirst($moduleEntity);

                $qryCtx = new FindQueryContext($entityName, $reqCtx);
                $qryCtx->addField(new RelativeField('*'));
                $qryCtx->addFilter(new StringFilter($moduleEntity, '', 'id = :id'));
                $qryCtx->setParams(['id' => $params['id']]);

                $context[lcfirst($entityName)] = $entity = $qryCtx->findOne();

                unset($params['id']);

                $helper = new BasicHelper($context->getApplicationContext());
                $helper->basicSetValues($entity, $params);

                $postUpdateCallable($context, $action, $entity);

                return $entity;

            });

    }

}