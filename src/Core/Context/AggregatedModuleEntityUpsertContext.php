<?php


namespace Core\Context;


use Core\Error\Error;
use Core\Error\ValidationException;
use Core\Helper\AggregatedModuleEntity\Helper;
use Core\Module\AggregatedModuleEntityDefinition;
use Core\Module\ModelAspect;
use Core\Module\ModuleEntity;
use Core\Parameter\UnsafeParameter;
use Core\Validation\Parameter\Object;
use Core\Validation\Validator;

class AggregatedModuleEntityUpsertContext extends ModuleEntityUpsertContext {

    /**
     * @var array
     */
    protected $childrenUpsertContexts = [];

    /**
     * @var string[]
     */
    protected $disabledModelAspects = [];

    /**
     * @param AggregatedModuleEntityDefinition $definition
     * @param int|null                         $entityId
     * @param array                            $params
     * @param ActionContext                    $actionContext
     */
    public function __construct (AggregatedModuleEntityDefinition $definition, $entityId = NULL, array $params,
                                 ActionContext $actionContext) {

        parent::__construct($definition, $entityId, $params, $actionContext);

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

        return array_filter($this->getDefinition()->getModelAspects(), function (ModelAspect $modelAspect) {

            return !in_array($modelAspect->getPrefix(), $this->disabledModelAspects);

        });

    }

    /**
     * @return \Core\Module\ModelAspect[]
     */
    public function getDisabledAspects () {

        return array_filter($this->getDefinition()->getModelAspects(), function (ModelAspect $modelAspect) {

            return in_array($modelAspect->getPrefix(), $this->disabledModelAspects);

        });

    }

    /**
     * @param string $prefix
     */
    public function disableAspect ($prefix) {

        $this->disabledModelAspects[] = $prefix;

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

        return parent::getDefinition();

    }

    /**
     * @param bool $shouldThrowException
     */
    public function validateParams ($shouldThrowException = false) {

        $this->params = $this->validateAggregatedStructure($this->getParams());
        parent::validateParams($shouldThrowException);

    }

    /**
     * @param array $params
     *
     * @return array
     * @throws ValidationException
     */
    protected function validateAggregatedStructure (array $params) {

        foreach ($this->getEnabledAspects() as $modelAspect) {

            $explodedPrefix = explode('.', $modelAspect->getPrefix());
            $data = $params;
            foreach ($explodedPrefix as $elem) {
                if (is_object($data) || !isset($data[$elem])) {
                    continue 2;
                }
                $data = $data[$elem];
            }
            $finalValue = UnsafeParameter::getFinalValue($data);

            $validationResult = Validator::validateValue($finalValue, [new Object()], $modelAspect->getPrefix());
            $validationResult->throwIfErrors();

            // TODO : refactor, use $validationResult->getValidatedParams()
            if ($data != $finalValue) {
                // TODO: check if ArrayExtra::magicalSet shouldn't be used
                Helper::setFinalValue($params, $explodedPrefix, $finalValue);
            }

        }

        return $params;

    }

}