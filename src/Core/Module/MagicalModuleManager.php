<?php

namespace Core\Module;

use Core\Action\SimpleAction;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Field\RelativeField;
use Core\Filter\Filter;
use Core\Util\ModelConverter;

abstract class MagicalModuleManager extends ModuleManager {

    /**
     * @param Filter[] $filters
     *
     * @return mixed
     */
    public function magicalDelete ($filters) {

        $qryCtx = new FindQueryContext($this->getMainEntityName(), new RequestContext());

        $qryCtx->addField(new RelativeField('*'));

        foreach ($filters as $filter) {
            $qryCtx->addFilter($filter);
        }

        $results = $qryCtx->findAll();

        foreach ($results as $model) {

            $modelName = end(explode('\\', get_class($model)));

            $this->getModelModuleEntity($modelName)->delete($model);
        }

    }

    /**
     * @return string
     */
    private function getMainEntityName () {

        foreach ($this->getModelAspects() as $modelAspect) {
            if ($modelAspect->getRelativeField() == '') {
                return $modelAspect->getModel();
            }
        }

        return NULL;

    }

    /**
     * @param RequestContext $requestContext
     * @param String[]       $fields
     * @param Filter[]       $filters
     * @param array          $params
     * @param Boolean        $hydrateArray
     * @param string[]       $disabledKeyPaths
     *
     * @return mixed
     * @throws \Core\Error\FormattedError
     */
    protected function magicalFind (RequestContext $requestContext, array $fields = [], array $filters = [],
                                    array $params = [], /*deprecated*/
                                    $hydrateArray = false, array $disabledKeyPaths = []) {


        $appCtx = ApplicationContext::getInstance();

        $qryCtx = new FindQueryContext($this->mainEntityName, $requestContext);

        $fields = $this->formatFindValues($fields);
        $this->disableModelAspects($disabledKeyPaths);

        foreach ($fields as $value) {
            $qryCtx->addField(new RelativeField($value));
        }

        $returnedFields = $this->convertRequestedFields($requestContext);

        foreach ($filters as $filter) {
            $qryCtx->addFilter($filter);
        }

        $qryCtx->setParams($params);

        $result = $qryCtx->findAll();


        if ($hydrateArray) {
            foreach ($result as &$data) {
                if (is_array($data)) {
                    foreach ($data as &$object) {
                        if (is_object($object)) {
                            $object =
                                (new ModelConverter($appCtx))->toArray($object, array_merge($fields, $returnedFields));
                        }
                    }
                }
                else {
                    $data = (new ModelConverter($appCtx))->toArray($data, array_merge($fields, $returnedFields));
                }
            }
        }

        $result = $hydrateArray ? $this->formatFindResultArray($result) : $this->formatFindResultToObject($result);

        $this->enableModelAspects();


        return $result;

    }

    /**
     * @param array $disabledRelativeFields
     */
    private function disableModelAspects ($disabledRelativeFields) {

        if (!is_array($disabledRelativeFields) || count($disabledRelativeFields) < 1) {
            return;
        }

        foreach ($this->modelAspects as $modelAspect) {
            if (!$modelAspect->getRelativeField()) {
                $newModelAspects[] = $modelAspect;
                continue;
            }
            $relativeField = $modelAspect->getRelativeField()->getValue();
            foreach ($disabledRelativeFields as $disabledRelativeField) {
                if (strpos($relativeField, $disabledRelativeField) === 0) {
                    $modelAspect->disable();
                }
            }

        }

    }

    private function enableModelAspects () {

        foreach ($this->modelAspects as $modelAspect) {
            if (!$modelAspect->isEnabled()) {
                $modelAspect->enable();
            }
        }

    }

    /**
     * @param string   $name
     * @param array    $params
     * @param callable $processFn
     */
    protected function defineAction ($name, array $params, callable $processFn) {

        $module = $this->getModuleName();
        $appCtx = ApplicationContext::getInstance();
        $appCtx->addAction(new SimpleAction($module, $name, [], $params, $processFn));

    }

    /**
     * @return array
     */
    protected function getModuleName () {

        $className = get_called_class();
        $classNameExploded = explode('\\', $className);

        return $classNameExploded[count($classNameExploded) - 2];

    }

    /**
     * Load model aspects of each AggregatedModuleEntity
     * This must be done once every ModuleEntities are loaded
     * @param ApplicationContext $context
     */
    public function loadModelAspects(ApplicationContext $context) {

        // loading of model aspect must be done after the definition of all Module Entities
        foreach ($this->moduleEntities as $moduleEntity) {
            $moduleEntityDefinition = $moduleEntity->getDefinition();
            if ($moduleEntityDefinition instanceof AggregatedModuleEntityDefinition) {
                $moduleEntityDefinition->loadModelAspects($context);
            }
        }

    }

}