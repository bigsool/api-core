<?php


namespace Core\Module;


use Core\Context\ActionContext;
use Core\Context\AggregatedModuleEntitySerializerContext;
use Core\Context\AggregatedModuleEntityUpsertContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Helper\AggregatedModuleEntity\EntityParamsTranslatorHelper;
use Core\Helper\AggregatedModuleEntity\UpsertContextHelper;
use Core\Helper\AggregatedModuleEntity\UpsertHelper;
use Core\Registry;

class AggregatedModuleEntity extends AbstractModuleEntity {

    /**
     * @var array
     */
    protected $keysToRemove = [];

    /**
     * @param ApplicationContext               $applicationContext
     * @param AggregatedModuleEntityDefinition $definition
     */
    public function __construct (ApplicationContext $applicationContext, AggregatedModuleEntityDefinition $definition) {

        parent::__construct($applicationContext, $definition);

    }

    /**
     * @return AggregatedModuleEntitySerializerContext
     */
    public function getAggregatedSerializerContext () {

        return new AggregatedModuleEntitySerializerContext($this->getDefinition());

    }

    /**
     * @param array         $params
     * @param int           $entityId
     * @param ActionContext $actionContext
     *
     * @return AggregatedModuleEntityUpsertContext
     */
    protected function createUpsertContextProxy (array $params, $entityId, ActionContext $actionContext) {


        if (!$this->getDefinition()->getDBEntityName()) {
            throw new \RuntimeException('main entity undefined !');
        }

        // creates AggregatedUpsertContext
        // might perform some top level defaulting or validation
        $aggregatedUpsertContext =
            $this->getDefinition()->createUpsertContext($params, $entityId, $actionContext);


        // validate params of aggregated : constraints from aggregatedDefinition + structure validation
        $aggregatedUpsertContext->validateParams(true);
        $aggregatedParams = $aggregatedUpsertContext->getParams();


        // translates input params to match real entities names
        $translatedParams =
            EntityParamsTranslatorHelper::translatePrefixesToKeyPaths($aggregatedParams,
                                                                      $this->getDefinition()->getAllModelAspects());


        // handle main entity errors (ie user)
        UpsertContextHelper::addChildUpsertContextForModelAspect($this->getDefinition()->getMainAspect(),
                                                                 $aggregatedUpsertContext,
                                                                 $translatedParams, $actionContext);

        // we don't want the main aspect here
        foreach ($aggregatedUpsertContext->getEnabledAspects() as $modelAspect) {

            // we need this line for Credential (enabled in case of create, disabled in case of update)
            if ($modelAspect->isDisabledForAction($aggregatedUpsertContext->isCreation() ? 'create' : 'update')) {
                continue;
            }

            // handle sub entities errors, ie company
            UpsertContextHelper::addChildUpsertContextForModelAspect($modelAspect, $aggregatedUpsertContext,
                                                                     $translatedParams, $actionContext);

        }

        return $aggregatedUpsertContext;

    }

    /**
     * @param MagicalEntity $entity
     */
    public function delete ($entity) {

        if (!($entity instanceof MagicalEntity)) {

            throw new \InvalidArgumentException(sprintf('$entity must be an MagicalEntity, %s %s given',
                                                        gettype($entity), get_class($entity)));

        }

        parent::delete($entity->getMainEntity());

    }

    /**
     * @param FindQueryContext $findQueryContext
     *
     * @return array
     */
    public function find (FindQueryContext $findQueryContext) {

        $findQueryContext->setEntity($this->getDefinition()->getDBEntityName());

        EntityParamsTranslatorHelper::translatedRequestedFieldsInRequestContext($findQueryContext->getRequestContext(),
                                                                                $this->getDefinition()
                                                                                     ->getAllModelAspects());

        $results = parent::find($findQueryContext);

        foreach ($results as &$result) {
            if (is_object($result)) {
                $result = $this->getMagicalEntityObject($result);
            }
        }

        return $results;

    }

    /**
     * @return AggregatedModuleEntityDefinition
     */
    public function getDefinition () {

        return parent::getDefinition();

    }

    /**
     * @param MagicalEntity             $entity
     * @param ModuleEntityUpsertContext $upsertContext
     */
    protected function postModifyProxy ($entity, ModuleEntityUpsertContext $upsertContext) {

        if (!($upsertContext instanceof AggregatedModuleEntityUpsertContext)) {
            throw new \RuntimeException('$upsertContext must be a AggregatedModuleEntityUpsertContext');
        }

        foreach ($upsertContext->getChildrenUpsertContextsWithModuleEntities() as $childContextContext) {
            /**
             * @var ModuleEntityUpsertContext $childContext
             */
            $childContext = $childContextContext[0];

            /**
             * @var ModuleEntity $moduleEntity
             */
            $moduleEntity = $childContextContext[1];

            $moduleEntity->getDefinition()->postModify($childContext->getEntity(), $childContext);
        }

        $this->getDefinition()->postModify($upsertContext->getEntity(), $upsertContext);
    }

    /**
     * @param ModuleEntityUpsertContext $upsertContext
     *
     * @return MagicalEntity
     */
    protected function upsert (ModuleEntityUpsertContext $upsertContext) {

        if (!($upsertContext instanceof AggregatedModuleEntityUpsertContext)) {
            throw new \RuntimeException('AggregatedModuleEntityUpsertContext required');
        }

        foreach ($upsertContext->getChildrenUpsertContextsWithModelAspect() as $childContextWithModelAspect) {

            /**
             * @var ModuleEntityUpsertContext $childContext
             * @var ModelAspect               $modelAspect
             */
            $modelAspect = $childContextWithModelAspect[1];
            $childContext = $childContextWithModelAspect[0];

            // if entity already assign
            if ($childContext->isCreation() && $childContext->getEntity()) {
                continue;
            }
            else {
                /**
                 * TODO : $moduleEntity is a ModuleEntity not an AbstractModuleEntity
                 * @var AbstractModuleEntity $moduleEntity
                 */
                $moduleEntity = $modelAspect->getModuleEntity();
                $subEntity = $moduleEntity->upsert($childContext);
            }
            $childContext->setEntity($subEntity);

        }

        UpsertHelper::setRelationshipsFromMetadata($upsertContext);

        $entity = $this->getMagicalEntityObject($upsertContext->getMainEntity());
        $upsertContext->setEntity($entity);

        return $entity;

    }

    /**
     * @param $entity
     *
     * @return MagicalEntity
     */
    protected function getMagicalEntityObject ($entity) {

        $className = Registry::realModelClassName($this->getDefinition()->getEntityName());

        return new $className($entity);

    }



}