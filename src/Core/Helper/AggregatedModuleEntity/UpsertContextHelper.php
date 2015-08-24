<?php


namespace Core\Helper\AggregatedModuleEntity;


use Core\Context\ActionContext;
use Core\Context\AggregatedModuleEntityUpsertContext;
use Core\Context\FindQueryContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Error\ValidationException;
use Core\Filter\StringFilter;
use Core\Module\AggregatedModuleEntityDefinition;
use Core\Module\ModelAspect;
use Core\Parameter\UnsafeParameter;

class UpsertContextHelper {

    /**
     * @param ModelAspect                         $modelAspect
     * @param AggregatedModuleEntityUpsertContext $aggregatedUpsertContext
     * @param array                               $translatedParams
     * @param ActionContext                       $actionContext
     *
     * @throws ValidationException
     */
    public static function addChildUpsertContextForModelAspect (ModelAspect $modelAspect,
                                                                AggregatedModuleEntityUpsertContext $aggregatedUpsertContext,
                                                                array $translatedParams, ActionContext $actionContext) {

        $aspectParams =
            static::getEntityParamsForAspect($aggregatedUpsertContext->getDefinition(), $modelAspect,
                                             $translatedParams);

        $moduleEntity = $modelAspect->getModuleEntity();

        // for modification, we need to find the correct $entityId for sub entity
        $entityId = NULL;
        if ($aggregatedUpsertContext->isUpdate()) {
            if ($modelAspect->isMainAspect()) {
                $entityId = $aggregatedUpsertContext->getEntityId();
            }
            else {
                $subEntity =
                    static::getEntityFromKeyPath($aggregatedUpsertContext->getMainEntity(),
                                                 $modelAspect->getRelativeField());
                $entityId = $subEntity->getId();
            }

        }

        // TODO : handle sub-sub-entities (company.contact if company id is provided)
        // in case of creation, we should handle id instead of object definition (ie: company for sub-user)
        if ($aggregatedUpsertContext->isCreation() && array_key_exists('id', $aspectParams)
            && !($modelAspect->isMainAspect())
        ) {
            // TODO : has he the right to do it ? (assign)
            $subEntityId = UnsafeParameter::getFinalValue($aspectParams['id']);
            $reqCtx = $aggregatedUpsertContext->getActionContext()->getRequestContext()->copyWithoutRequestedFields();
            $qryCtx = new FindQueryContext($modelAspect->getModel(), $reqCtx);
            $qryCtx->addField('id');
            $qryCtx->addFilter(new StringFilter($modelAspect->getModel(), '', 'id = :id'), $subEntityId);
            $subEntity = $qryCtx->findOne();

            $childUpsertContext =
                new ModuleEntityUpsertContext($moduleEntity->getDefinition(), $entityId, $aspectParams, $actionContext);
            $childUpsertContext->setEntity($subEntity);
        }
        else {
            try {
                $childUpsertContext =
                    $moduleEntity->getDefinition()->createUpsertContext($aspectParams, $entityId, $actionContext);
            }
            catch (ValidationException $exception) {
                $aggregatedUpsertContext->addErrors($exception->getErrors(), $modelAspect);
                throw new ValidationException($aggregatedUpsertContext->getErrors());
            }
        }

        $aggregatedUpsertContext->addErrors($childUpsertContext->getErrors(), $modelAspect);

        $aggregatedUpsertContext->addChildUpsertContext($childUpsertContext, $moduleEntity, $modelAspect);
    }

    /**
     * Returns entity only related fields from input translated params
     *
     * @param AggregatedModuleEntityDefinition $definition
     * @param ModelAspect                      $modelAspect
     * @param array                            $translatedParams
     *
     * @return array
     */
    protected static function getEntityParamsForAspect (AggregatedModuleEntityDefinition $definition,
                                                        ModelAspect $modelAspect, array $translatedParams) {

        $entityParams = [];

        $relativeField = $modelAspect->getRelativeField();
        if ($relativeField) {
            $explodedKeyPath = explode('.', $relativeField);
            $data = $translatedParams;
            foreach ($explodedKeyPath as $elem) {
                if (!isset($data[$elem])) {
                    $data = [];
                    break;
                }
                $data = $data[$elem];
            }
            $translatedParams = $data;
        }

        // TODO : check why
        $translatedParams = UnsafeParameter::getFinalValue($translatedParams);
        // prepares sub context by filtering out non company related fields
        foreach ($translatedParams as $key => $value) {
            $isParamLinkedToAspectModel = static::isParamLinkedToAspectModel($definition, $relativeField, $key);
            $isParamLinkedToAggregated = $relativeField || !array_key_exists($key, $definition->getConstraintsList());
            if (!$isParamLinkedToAspectModel && $isParamLinkedToAggregated) {
                $entityParams[$key] = $value;
            }
        }

        return $entityParams;

    }

    /**
     * @param AggregatedModuleEntityDefinition $definition
     * @param string                           $relativeField
     * @param string                           $paramKey
     *
     * @return boolean
     */
    protected static function isParamLinkedToAspectModel (AggregatedModuleEntityDefinition $definition, $relativeField,
                                                          $paramKey) {

        $relativeField = $relativeField ? $relativeField . '.' . $paramKey : $paramKey;

        foreach ($definition->getModelAspects() as $modelAspect) {
            if ($modelAspect->getRelativeField() == $relativeField) {
                return true;
            }
        }

        return false;

    }

    /**
     * @param               $entity
     * @param string        $relativeField
     *
     * @return mixed
     */
    protected static function getEntityFromKeyPath ($entity, $relativeField) {

        $models = explode('.', $relativeField);

        if ($entity) {
            foreach ($models as $model) {
                $fn = 'get' . ucfirst($model);
                $entity = $entity->$fn();
            }

        }

        return $entity;

    }

}