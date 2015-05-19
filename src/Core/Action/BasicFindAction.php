<?php

namespace Core\Action;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Module\ModuleEntity;

class BasicFindAction extends SimpleAction {

    /**
     * @param string $module
     * @param ModuleEntity $moduleEntity
     * @param array $minRights
     * @param array $params
     * @param callable $preFindCallable
     * @param callable $postFindCallable
     *
     */
    public function __construct ($module, ModuleEntity $moduleEntity, $minRights, array $params,
                                 callable $preFindCallable = NULL,
                                 callable $postFindCallable = NULL) {

        if (!$preFindCallable) {
            $preFindCallable = function () {
            };
        }

        if (!$postFindCallable) {
            $postFindCallable = function () {
            };
        }

        parent::__construct($module, 'find', $minRights, $params,
            function (ActionContext $context, BasicFindAction $action) use (
                &$moduleEntity, &$helperName, &$preFindCallable, &$postFindCallable
            ) {

                $preFindCallable($context, $action);

                $reqCtx = $context->getRequestContext()->copyWithoutRequestedFields();

                $entityName = $moduleEntity->getEntityName();

                $moduleEntity = ucfirst($moduleEntity);

                $findQueryContext = new FindQueryContext($entityName, $reqCtx);
                $findQueryContext->addField('*');

                $context[$entityName] = $entities = $findQueryContext->findAll();

                $postFindCallable($context, $action, $entities);

                // TODO: pluralise $model

                return $entities;

            });

    }

}