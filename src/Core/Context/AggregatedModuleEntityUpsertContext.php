<?php


namespace Core\Context;


use Core\Error\Error;
use Core\Module\AggregatedModuleEntityDefinition;
use Core\Module\ModelAspect;
use Core\Module\ModuleEntity;

class AggregatedModuleEntityUpsertContext extends ModuleEntityUpsertContext {

    /**
     * @var ModuleEntityUpsertContext[]
     */
    protected $childrenUpsertContexts = [];

    /**
     * @var ModelAspect[]
     */
    protected $disabledModelAspects = [];

    /**
     * @param AggregatedModuleEntityDefinition $definition
     * @param int|null                         $entityId
     * @param ActionContext                    $actionContext
     */
    public function __construct (AggregatedModuleEntityDefinition $definition, $entityId = NULL,
                                 ActionContext $actionContext) {

        parent::__construct($definition, $entityId, $actionContext);

    }

    /**
     * @return ModuleEntityUpsertContext[]
     */
    public function getChildrenUpsertContexts () {

        $childrenContexts = [];
        foreach ($this->childrenUpsertContexts as $childContext) {
            $childrenContexts[] = $childContext['upsertContext'];
        }

        return $childrenContexts;

    }

    /**
     * @return array
     */
    public function getChildrenUpsertContextsWithModuleEntities () {

        $childrenContexts = [];
        foreach ($this->childrenUpsertContexts as $childContext) {
            $childrenContexts[] = [$childContext['upsertContext'], $childContext['moduleEntity']];
        }

        return $childrenContexts;
    }

    /**
     * @return array
     */
    public function getChildrenUpsertContextsWithModelAspect () {

        $childrenContexts = [];
        foreach ($this->childrenUpsertContexts as $childContext) {
            $childrenContexts[] = [$childContext['upsertContext'], $childContext['modelAspect']];
        }

        return $childrenContexts;
    }

    /**
     * @param ModuleEntityUpsertContext $ctx
     * @param ModuleEntity              $moduleEntity
     * @param ModelAspect               $modelAspect
     */
    public function addChildUpsertContext (ModuleEntityUpsertContext $ctx, ModuleEntity $moduleEntity,
                                           ModelAspect $modelAspect) {

        // TODO : this is a bit ugly, consider refactoring
        $this->childrenUpsertContexts[] =
            ['upsertContext' => $ctx, 'moduleEntity' => $moduleEntity, 'modelAspect' => $modelAspect];
    }

    /**
     * @return \Core\Module\ModelAspect[]
     */
    public function getAllEnabledModelAspects () {

        return array_merge([$this->getDefinition()->getMainAspect()], $this->getEnabledAspects());

    }

    /**
     * @return \Core\Module\ModelAspect[]
     */
    public function getEnabledAspects () {

        return array_diff($this->getDefinition()->getModelAspects(), $this->getDisabledAspects());

    }

    /**
     * @return \Core\Module\ModelAspect[]
     */
    public function getDisabledAspects () {

        return $this->disabledModelAspects;

    }

    /**
     * @return mixed|null
     */
    public function getMainEntity () {

        foreach ($this->childrenUpsertContexts as $childContext) {
            /**
             * @var ModelAspect $modelAspect
             */
            $modelAspect = $childContext['modelAspect'];
            if ($modelAspect->isMainAspect()) {
                /**
                 * @var ModuleEntityUpsertContext $upsertContext
                 */
                $upsertContext = $childContext['upsertContext'];

                return $upsertContext->getEntity();
            }
        }

        return NULL;

    }

    /**
     * @param Error[]     $errors
     * @param ModelAspect $aspect
     */
    public function addErrors (array $errors, ModelAspect $aspect = NULL) {

        if ($aspect && !$aspect->isMainAspect()) {
            foreach ($errors as $error) {
                $separator = $aspect->isWithPrefixedFields() ? '_' : '.';
                $error->setField($aspect->getPrefix() . $separator . $error->getField());
            }
        }

        parent::addErrors($errors);

    }

    /**
     * @return AggregatedModuleEntityDefinition
     */
    public function getDefinition () {

        parent::getDefinition();

    }

}