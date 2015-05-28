<?php


namespace Core\Module;


use Core\Context\ActionContext;
use Core\Context\AggregatedModuleEntityUpsertContext;
use Core\Context\AggregatedSerializerContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Error\ValidationException;
use Core\Helper\AggregatedEntityParamsTranslatorHelper;
use Core\Helper\AggregatedModuleEntityHelper;
use Core\Helper\AggregatedUpsertContextHelper;
use Core\Helper\AggregatedUpsertHelper;
use Core\Parameter\UnsafeParameter;
use Core\Registry;
use Core\Validation\RuntimeConstraintsProvider;
use Symfony\Component\Config\Definition\Exception\Exception;

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
     * @return AggregatedSerializerContext
     */
    public function getAggregatedSerializerContext() {

        return new AggregatedSerializerContext($this->getDefinition());

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
     * @param MagicalEntity                       $entity
     * @param AggregatedModuleEntityUpsertContext $aggregatedModuleEntityUpsertContext
     */
    protected function postModifyProxy ($entity,
                                        AggregatedModuleEntityUpsertContext $aggregatedModuleEntityUpsertContext) {

        foreach ($aggregatedModuleEntityUpsertContext->getChildrenUpsertContexts() as $childContextContext) {
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

        $this->getDefinition()
             ->postModify($aggregatedModuleEntityUpsertContext->getEntity(), $aggregatedModuleEntityUpsertContext);
    }

    /**
     * @param mixed $entity
     */
    public function save ($entity) {

        if (!($entity instanceof MagicalEntity)) {
            throw new \RuntimeException(sprintf('$entity must be an MagicalEntity, %s %s given', gettype($entity),
                                                get_class($entity)));
        }

        parent::save($entity->getMainEntity());

    }

    /**
     * @param array         $unsafeParams
     * @param int           $entityId
     * @param ActionContext $actionContext
     *
     * @return AggregatedModuleEntityUpsertContext
     */
    protected function createUpsertContextProxy (array $unsafeParams, $entityId, ActionContext $actionContext) {


        $action = ($entityId == NULL) ? 'create' : 'update';

        if (!$this->getDefinition()->getDBEntityName()) {
            throw new \RuntimeException('main entity undefined !');
        }

        // validates structure of input params
        // WARNING : disabled aspects are not yet handled

        $validatedParams = $this->validateAggregatedStructure($unsafeParams, $action);

        // translates input params to match real entities names
        $translatedParams =
            AggregatedEntityParamsTranslatorHelper::translatePrefixesToKeyPaths($validatedParams, $this->getDefinition()
                                                                                                       ->getAllModelAspects());

        // creates AggregatedUpsertContext
        // might perform some top level defaulting or validation
        /**
         * @var AggregatedModuleEntityUpsertContext $aggregatedUpsertContext
         */

        $aggregatedUpsertContext =
            $this->getDefinition()->createUpsertContext($translatedParams, $entityId, $actionContext);


        // handle main entity errors (ie user)
        AggregatedUpsertContextHelper::addChildUpsertContextForModelAspect($this->getDefinition()->getMainAspect(),
                                                                           $aggregatedUpsertContext,
                                                                           $translatedParams, $actionContext);

        // we don't want the main aspect here
        foreach ($aggregatedUpsertContext->getEnabledAspects() as $modelAspect) {

            if ($modelAspect->isDisabledForAction($action)) {
                continue;
            }

            // handle sub entities errors, ie company
            AggregatedUpsertContextHelper::addChildUpsertContextForModelAspect($modelAspect, $aggregatedUpsertContext,
                                                                               $translatedParams, $actionContext);

        }

        return $aggregatedUpsertContext;

    }




    /**
     * @param ModuleEntityUpsertContext $aggregatedModuleEntityUpsertContext
     *
     * @return MagicalEntity
     */
    protected function upsert (ModuleEntityUpsertContext $aggregatedModuleEntityUpsertContext) {

        if (!($aggregatedModuleEntityUpsertContext instanceof AggregatedModuleEntityUpsertContext)) {
            throw new \RuntimeException('AggregatedModuleEntityUpsertContext required');
        }

        foreach ($aggregatedModuleEntityUpsertContext->getChildrenUpsertContextsWithModuleEntities() as $childContextWithModuleEntity) {
            /**
             * @var ModuleEntityUpsertContext $childContext
             */
            $childContext = $childContextWithModuleEntity[0];

            /**
             * @var AbstractModuleEntity $moduleEntity
             */
            $moduleEntity = $childContextWithModuleEntity[1];

            $subEntity = $moduleEntity->upsert($childContext);
            $childContext->setEntity($subEntity);

        }

        AggregatedUpsertHelper::setRelationshipsFromMetadata($aggregatedModuleEntityUpsertContext);

        $entity = $this->getMagicalEntityObject($aggregatedModuleEntityUpsertContext->getMainEntity());
        $aggregatedModuleEntityUpsertContext->setEntity($entity);

        return $entity;

    }

    /**
     * @param array $params
     * @param string $action
     *
     * @return array
     */
    protected function validateAggregatedStructure ($params, $action) {

        foreach ($this->getDefinition()->getModelAspects() as $modelAspect) {

            $explodedPrefix = explode('.', $modelAspect->getPrefix());
            $data = $params;
            foreach ($explodedPrefix as $elem) {
                if (is_object($data) || !isset($data[$elem])) {
                    continue 2;
                }
                $data = $data[$elem];
            }
            $name = $explodedPrefix[count($explodedPrefix) - 1];
            $constraints = $modelAspect->getConstraints();
            if (count($constraints) == 1) {
                $constraints = $constraints[$action];


                $validator = new RuntimeConstraintsProvider([$name => $constraints]);
                // TODO: fix me
                if ( ! $validator->validate(UnsafeParameter::getFinalValue($data), $modelAspect->getPrefix()) ) {
                    throw;
                }

            }

            $finalValue = UnsafeParameter::getFinalValue($data);
            if ($data != $finalValue) {
                // TODO: check if ArrayExtra::magicalSet shouldn't be used
                AggregatedModuleEntityHelper::setFinalValue($params, $explodedPrefix, $finalValue);
            }

        }

        return $params;

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