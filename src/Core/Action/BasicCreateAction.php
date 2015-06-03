<?php

namespace Core\Action;


use Core\Context\ActionContext;
use Core\Module\ModuleEntity;

class BasicCreateAction extends SimpleAction {

    /**
     * @param string       $module
     * @param ModuleEntity $moduleEntity
     * @param array        $minRights
     * @param array        $params
     * @param callable     $preCreateCallable
     * @param callable     $postCreateCallable
     *
     */
    public function __construct ($module, ModuleEntity $moduleEntity, $minRights, array $params,
                                 callable $preCreateCallable = NULL,
                                 callable $postCreateCallable = NULL) {

        if (!$preCreateCallable) {
            $preCreateCallable = function () {
            };
        }

        if (!$postCreateCallable) {
            $postCreateCallable = function () {
            };
        }

        parent::__construct($module, 'create', $minRights, $params,
            function (ActionContext $context, BasicCreateAction $action) use (
                &$moduleEntity, &$helperName, &$preCreateCallable, &$postCreateCallable
            ) {

                $preCreateCallable($context, $action);

                $params = $context->getVerifiedParams();

                $entityObj = $moduleEntity->create($params, $context);
                $moduleEntity->save($entityObj);

                $entityName = $moduleEntity->getDefinition()->getEntityName();

                $context[lcfirst($entityName)] = $entityObj;

                $postCreateCallable($context, $action, $entityObj);

                return $entityObj;

            });

    }

}