<?php

namespace Core\Module;

use Core\Action\Action;
use Core\Action\SimpleAction;
use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\RequestContext;
use Core\Field\RelativeField;
use Core\Filter\Filter;
use Core\Parameter\UnsafeParameter;
use Core\Registry;
use Core\Util\ArrayExtra;
use Core\Util\ModelConverter;
use Core\Validation\Parameter\Constraint;
use Core\Validation\RuntimeConstraintsProvider;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\Yaml\Exception\RuntimeException;

abstract class MagicalModuleManager extends ModuleManager {

    /**
     * @var ModelAspect[]
     */
    private $modelAspects = [];

    /**
     * @var array
     */
    private $models = [];

    /**
     * @var string
     */
    private $mainEntityName = NULL;

    /**
     * @var mixed
     */
    private $mainEntity = NULL;

    /**
     * @var array
     */
    private $keysToRemove = [];

    /**
     * @var ModelAspect[]
     */
    private $modelAspectsWithoutParams = [];


    /**
     * @param ActionContext $ctx
     * @param string[]      $disabledKeyPaths
     *
     * @return mixed
     */
    public function magicalCreate (ActionContext $ctx, array $disabledKeyPaths = []) {

        $this->disableModelAspects($disabledKeyPaths);
        $result = $this->magicalModify($ctx, 'create');
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

    /**
     * @param ActionContext $ctx
     * @param string        $action
     *
     * @return MagicalEntity
     */
    protected function magicalModify (ActionContext $ctx, $action) {

        if (!$this->mainEntityName) {
            throw new RuntimeException('main entity undefined !');
        }

        $prefixes = [];
        foreach ($this->getModelAspects() as $modelAspect) {
            $prefixes[] = $modelAspect->getPrefix();
        }

        $params = $ctx->getParams();

        $validatedParams = $this->validateParams($params, $action);
        $formattedParams = $this->formatModifyParams($validatedParams);

        foreach ($this->getModelAspects() as $modelAspect) {

            // TODO HANDLE ONETOMANY
            $actions = $modelAspect->getActions();

            $modifyAction = array_key_exists($action, $actions) ? $actions[$action] : 'none';

            if ($modifyAction != 'none' && $actions[$action] == NULL) {
                continue;
            }

            $params = NULL;
            if ($modelAspect->getRelativeField()) {
                $explodedKeyPath = explode('.', $modelAspect->getRelativeField()->getValue());
                $params = $formattedParams;
                $data = $params;
                foreach ($explodedKeyPath as $elem) {
                    if (!isset($data[$elem])) {
                        $data = [];
                        break;
                    }
                    $data = $data[$elem];
                }
                $params = $data;
            }


            if ($modifyAction == 'none') {

                $modifyAction = $this->getMagicalAction($action, $modelAspect);

            }

            $subContext = NULL;

            if (is_array($params) || $params != NULL) {
                if (count($params) == 0) {
                    $this->modelAspectsWithoutParams[] = $modelAspect;
                    continue;
                }
                $subContext = new ActionContext($ctx);
                $subContext->clearParams();
                $params = UnsafeParameter::getFinalValue($params);

                foreach ($params as $key => $value) {
                    if (!$this->isParamLinkedToAspectModel($modelAspect->getRelativeField()->getValue(), $key)) {
                        $subContext->setParam($key, $value);
                    }
                }
                if (!$this->isMainEntity($modelAspect) && $action == 'update') {
                    $entity = $this->getEntityFromKeyPath($modelAspect->getRelativeField());//TODO// ADD TESTS
                    if ($entity) {
                        $subContext->setParam('id', $entity->getId());
                    }
                }

            }
            else {

                $subContext = new ActionContext($ctx);
                $subContext->clearParams();

                foreach ($formattedParams as $key => $value) {
                    if (!$this->isParamLinkedToAspectModel(NULL, $key)) {
                        $subContext->setParam($key, $value);
                    }
                }

            }
            $params = $subContext->getParams();
            $result = $modifyAction->process($subContext);

            $key = $modelAspect->getRelativeField() ? $modelAspect->getRelativeField()->getValue() : 'main';
            $this->models[$key] = $result;

            if ($this->isMainEntity($modelAspect)) {
                $this->mainEntity = $result;
            }

            if ($subContext) {
                $iterator = $subContext->getIterator();
                while ($iterator->valid()) {
                    $key = $iterator->key();
                    if (!isset($ctx[$key])) {
                        $ctx[$key] = $iterator->current();
                    }
                    $iterator->next();
                }
            }

        }

        if (!isset($this->mainEntity)) {
            return NULL;
        }

        $this->setRelationshipsFromMetadata();

        $this->saveEntities();

        return $this->getMagicalEntityObject($this->mainEntity);

    }

    /**
     * @return ModelAspect[]
     */
    private function getModelAspects () {

        return array_filter($this->modelAspects, function (ModelAspect $modelAspect) {

            return $modelAspect->isEnabled() && !in_array($modelAspect,$this->modelAspectsWithoutParams);

        });

    }

    /**
     * @param array  $params
     * @param string $action
     *
     * @return array
     */
    private function validateParams ($params, $action) {

        foreach ($this->getModelAspects() as $modelAspect) {

            if (!$modelAspect->getPrefix()) {
                continue;
            }
            $explodedPrefix = explode('.', $modelAspect->getPrefix());
            $data = $params;
            foreach ($explodedPrefix as $elem) {
                if (!isset($data[$elem])) {
                    continue 2;
                }
                $data = $data[$elem];
            }
            $name = $explodedPrefix[count($explodedPrefix) - 1];
            $constraints = $modelAspect->getConstraints();
            if (count($constraints) == 1) {
                $constraints = $constraints[$action];
                $validator = new RuntimeConstraintsProvider([$name => $constraints]);
                $isValid = $validator->validate($name, UnsafeParameter::getFinalValue($data), $name);
                if (!$isValid) {
                    throw new \RuntimeException('Model aspect contraints not respected'); //TODO//
                }
            }

            $finalValue = UnsafeParameter::getFinalValue($data);
            if ($data != $finalValue) {
                $this->setFinalValue($params, $explodedPrefix, $finalValue);;
            }

        }

        return $params;

    }

    /**
     * @param array $params
     * @param array $explodedPrefix
     * @param mixed $finalValue
     */
    private function setFinalValue (&$params, $explodedPrefix, $finalValue) {

        $currentPrefix = $explodedPrefix[0];

        if (count($explodedPrefix) == 1) {
            $params[$currentPrefix] = $finalValue;
        }
        else {
            array_splice($explodedPrefix, 0, 1);
            $this->setFinalValue($params[$currentPrefix], $explodedPrefix, $finalValue);
        }

    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function formatModifyParams ($params) {

        $formattedParams = [];

        foreach ($this->getModelAspects() as $modelAspect) {

            if ($modelAspect->getPrefix()) {
                $explodedPrefix = explode('.', $modelAspect->getPrefix());
                $data = $params;
                foreach ($explodedPrefix as $elem) {
                    if (!isset($data[$elem])) {
                        continue 2;
                    }
                    $data = $data[$elem];
                }
            }
            else {
                $data = $params;
            }

            if ($modelAspect->getRelativeField()) {
                $explodedKeyPath = explode('.', $modelAspect->getRelativeField()->getValue());
                $data = $this->buildArrayWithKeys($explodedKeyPath, $data);
            }

            $formattedParams = ArrayExtra::array_merge_recursive_distinct($formattedParams, $data);

            if ($modelAspect->getPrefix()
                && $modelAspect->getPrefix() != $modelAspect->getRelativeField()->getValue()
            ) {
                $formattedParams = $this->removeKeysFromArray($explodedPrefix, $formattedParams);
            }

        }

        $formattedParams = $this->handlePrefixedFields($formattedParams);

        return $formattedParams;

    }

    /**
     * @param array $keys
     * @param array $data
     *
     * @return array
     */
    public function buildArrayWithKeys ($keys, $data) {

        $tab = [];

        if (count($keys) == 1) {
            $tab[$keys[0]] = $data;
        }
        else {
            $tab[$keys[0]] = $this->buildArrayWithKeys(array_slice($keys, 1, count($keys)), $data);
        }

        return $tab;

    }

    /**
     * @param array $keysToRemove
     * @param array $data
     *
     * @return array
     */
    private function removeKeysFromArray ($keysToRemove, $data) {

        $newData = [];

        if (is_array($data)) {

            foreach ($data as $key => $value) {

                if (count($keysToRemove) == 1 && $keysToRemove[0] == $key) {
                    continue;
                }
                $newData[$key] =
                    $this->removeKeysFromArray(array_slice($keysToRemove, 1, count($keysToRemove)), $value);

            }

        }
        else {

            $newData = $data;

        }

        return $newData;

    }

    /**
     * @param array $params
     *
     * @return array
     */
    private function handlePrefixedFields ($params) {

        foreach ($this->getModelAspects() as $modelAspect) {

            $data = $params;
            $explodedRelativeField = [];

            if ($modelAspect->getRelativeField()) {
                $explodedRelativeField = explode('.', $modelAspect->getRelativeField()->getValue());
            }

            foreach ($explodedRelativeField as $elem) {
                if (!isset($data[$elem])) {
                    $data = [];
                    break;
                }
                $data = $data[$elem];
            }

            if ($data) {
                $params = $this->formatPrefixedFieldsToArray($params, $data);
            }

        }

        return $params;

    }

    /**
     * @param array $params
     * @param $data
     *
     * @return array
     */
    private function formatPrefixedFieldsToArray (array $params, $data) {

        foreach ($data as $key => $value) {

            if (strpos($key, '_') === false || is_array($value)) {
                continue;
            }

            $field = $key;

            for ($i = 0; ; ++$i) {

                $explodedKey = explode('_', $field);
                $prefix = implode('_', array_slice($explodedKey, 0, $i + 1));
                $prefix = str_replace('_', '.', $prefix);

                if ($i + 1 == count($explodedKey) - 1) {

                    if (($relativeField = $this->isLinkedToModel($prefix))) {
                        $explodedRelativeField = explode('.', $relativeField);
                        $explodedRelativeField[] = $explodedKey[count($explodedKey) - 1];
                        $data = $this->buildArrayWithKeys($explodedRelativeField, $value);
                        $params = ArrayExtra::array_merge_recursive_distinct($params, $data);
                    }

                    break;

                }

            }

            $this->keysToRemove[] = $field;

        }

        $params = $this->removePrefixedFields($params);

        return $params;

    }

    /**
     * @param string $prefix
     *
     * @return mixed
     */
    private function isLinkedToModel ($prefix) {

        foreach ($this->getModelAspects() as $modelAspect) {
            if ($modelAspect->getPrefix() && $modelAspect->getPrefix() == $prefix) {
                return $modelAspect->getRelativeField()->getValue();
            }
        }

        return false;

    }

    /**
     * @param mixed $params
     *
     * @return mixed
     */
    private function removePrefixedFields ($params) {

        if (is_array($params)) {
            foreach ($params as $key => $value) {
                if ($key && in_array($key, $this->keysToRemove)) {
                    unset($params[$key]);
                }
                else {
                    $params[$key] = $this->removePrefixedFields($value);
                }
            }
        }

        return $params;

    }

    /**
     * @param string      $action
     * @param ModelAspect $modelAspect
     *
     * @return Action
     */
    protected function getMagicalAction ($action, ModelAspect $modelAspect) {

        $appCtx = ApplicationContext::getInstance();

        $product = $appCtx->getProduct();
        try {
            $modifyAction = $appCtx->getAction($product . '\\' . $modelAspect->getModel(), $action, []);
        }
        catch (\RuntimeException $e) {
            $modifyAction = $appCtx->getAction('Core\\' . $modelAspect->getModel(), $action, []);
        }

        return $modifyAction;

    }

    /**
     * @param string $relativeField
     * @param string $paramKey
     *
     * @return boolean
     */
    private function isParamLinkedToAspectModel ($relativeField, $paramKey) {

        $relativeField = $relativeField ? $relativeField . '.' . $paramKey : $paramKey;

        foreach ($this->getModelAspects() as $modelAspect) {
            if (!$modelAspect->getRelativeField()) {
                continue;
            }
            if ($modelAspect->getRelativeField()->getValue() == $relativeField) {
                return true;
            }
        }

        return false;

    }

    /**
     * @param ModelAspect $modelAspect
     *
     * @return string
     */
    private function isMainEntity ($modelAspect) {

        return !$modelAspect->getRelativeField();

    }

    /**
     * @param RelativeField $relativeField
     *
     * @return mixed
     */
    private function getEntityFromKeyPath (RelativeField $relativeField) {

        $models = explode('.', $relativeField->getValue());

        $entity = $this->mainEntity;

        if ($entity) {
            foreach ($models as $model) {
                $fn = 'get' . ucfirst($model);
                $entity = $entity->$fn();
            }

        }

        return $entity;

    }

    private function setRelationshipsFromMetadata () {

        $modelNameForKeyPath = [];

        foreach ($this->getModelAspects() as $modelAspect) {

            if ($this->isMainEntity($modelAspect)) {
                continue;
            }

            $relativeField = $modelAspect->getRelativeField()->getValue();
            $modelNameForKeyPath[$relativeField] = $modelAspect->getModel();

            $pos = strrpos($relativeField, '.');

            if ($pos === false) {
                $modelName = $this->getMainEntityName();
                $lastKeyPath = $relativeField;
            }
            else {
                if (!isset($modelNameForKeyPath[substr($relativeField, 0, $pos)])) {
                    throw new \RuntimeException('model name not defined for this prefix');
                }
                $modelName = $modelNameForKeyPath[substr($relativeField, 0, $pos)];
                $lastKeyPath = substr($relativeField, $pos + 1);
            }

            $mainEntityClassName = Registry::realModelClassName($modelName);
            $metadata = ApplicationContext::getInstance()->getClassMetadata($mainEntityClassName);
            $mapping = $metadata->getAssociationMapping($lastKeyPath);

            $explodedKeyPath = explode('.', $relativeField);
            if (count($explodedKeyPath) == 1) {
                $sourceKeyPath = 'main';
            }
            else {
                array_pop($explodedKeyPath);
                $sourceKeyPath = implode('.', $explodedKeyPath);;
            }
            $targetKeyPath = $relativeField;

            $this->setRelationshipsFromAssociationMapping($sourceKeyPath, $targetKeyPath, $mapping);

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
     * @param string $sourceRelativeField
     * @param string $targetRelativeField
     * @param        $mapping
     */
    private function setRelationshipsFromAssociationMapping ($sourceRelativeField, $targetRelativeField,
                                                             array $mapping) {

        $field1 = $mapping['fieldName'];
        $field2 = isset($mapping['mappedBy']) ? $mapping['mappedBy'] : $mapping['inversedBy'];

        $prefix1 = 'set';
        $prefix2 = 'set';

        if ($mapping['type'] == ClassMetadataInfo::ONE_TO_MANY || $mapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {
            $field1 = substr($field1, 0, strlen($field1) - 1);
            $prefix1 = "add";
        }

        if ($mapping['type'] == ClassMetadataInfo::MANY_TO_ONE || $mapping['type'] == ClassMetadataInfo::MANY_TO_MANY) {
            $field2 = $mapping['inversedBy'];
            $field2 = substr($field2, 0, strlen($field2) - 1);
            $prefix2 = "add";
        }

        $fn = $prefix1 . ucfirst($field1);
        $this->models[$sourceRelativeField]->$fn($this->models[$targetRelativeField]);

        $fn = $prefix2 . ucfirst($field2);
        $this->models[$targetRelativeField]->$fn($this->models[$sourceRelativeField]);

    }

    private function saveEntities () {

        $appCtx = ApplicationContext::getInstance();

        $registry = $appCtx->getNewRegistry();

        foreach ($this->models as $model) {
            $registry->save($model);
        }

    }

    /**
     * @param string $entity
     *
     * @return string
     */
    public function getMagicalEntityObject ($entity) {

        $className = Registry::realModelClassName($this->getModuleName());

        return new $className($entity);

    }

    /**
     * @return array
     */
    protected function getModuleName () {

        $className = get_called_class();
        $classNameExploded = explode('\\', $className);

        return $classNameExploded[count($classNameExploded) - 2];

    }

    private function enableModelAspects () {

        foreach ($this->modelAspects as $modelAspect) {
            if (!$modelAspect->isEnabled()) {
                $modelAspect->enable();
            }
        }

    }

    /**
     * @param ActionContext $ctx
     * @param string[]      $disabledKeyPaths
     *
     * @return mixed
     */
    public function magicalUpdate (ActionContext $ctx, array $disabledKeyPaths = []) {

        $this->disableModelAspects($disabledKeyPaths);
        $result = $this->magicalModify($ctx, 'update');
        $this->enableModelAspects();


        return $result;

    }

    /**
     * @param ApplicationContext $context
     */
    public function load (ApplicationContext &$context) {

        $this->loadAspects();

        parent::load($context);

    }

    /**
     * @return mixed
     */
    public abstract function loadAspects ();

    /**
     * @param Filter[] $filters
     *
     * @return mixed
     */
    public function magicalDelete ($filters) {

        $appCtx = ApplicationContext::getInstance();

        $registry = $appCtx->getNewRegistry();

        $qryCtx = new FindQueryContext($this->getMainEntityName(), new RequestContext());

        $qryCtx->addField(new RelativeField('*'));

        foreach ($filters as $filter) {
            $qryCtx->addFilter($filter);
        }

        $result = $registry->find($qryCtx);

        $registry->delete($result[0]);

    }

    /**
     * @return ModelAspect[]
     */
    public function getAspects () {

        return $this->modelAspects;

    }

    /**
     * @param RequestContext $requestContext
     *
     * @return array
     */
    public function convertRequestedFields (RequestContext $requestContext) {

        $reqCtxReturnedFields = $requestContext->getReturnedFields();
        $reqCtxFormattedReturnedFields = [];
        $returnedFields = [];

        foreach ($reqCtxReturnedFields as $returnedField) {
            $returnedFields[] = $returnedField->getValue();
        }

        $returnedFields = $this->formatFindValues($returnedFields);

        foreach ($returnedFields as $returnedField) {
            $reqCtxFormattedReturnedFields[] = new RelativeField($returnedField);
        }

        $requestContext->setFormattedReturnedFields($reqCtxFormattedReturnedFields);

        return $returnedFields;
    }

    /**
     * @return mixed
     */
    protected function getMainEntity () {

        return $this->mainEntity;

    }

    /**
     * @param array $config
     */
    protected function setMainEntity ($config) {

        $this->addAspect($config);
        $this->mainEntityName = $config['model'];

    }

    /**
     * @param array $config
     */
    protected function addAspect (array $config) {

        $prefix = NULL;
        if (isset($config['prefix'])) {
            $prefix = $config['prefix'];
            if (!is_string($prefix)) {
                throw new \RuntimeException('invalid model');
            }
        }

        $model = NULL;
        if (isset($config['model'])) {
            $model = $config['model'];
            Registry::realModelClassName($model);
            if (!is_string($model)) {
                throw new \RuntimeException('invalid model');
            }
        }

        $relativeField = isset($config['keyPath']) ? new RelativeField($config['keyPath']) : NULL;
        if (!$relativeField) {
            foreach ($this->modelAspects as $modelAspect) {
                if (!$modelAspect->getRelativeField()) {
                    throw new \RuntimeException('two main entities');
                }
            }
        }

        $withPrefixedFields = isset($config['withPrefixedFields']) ? $config['withPrefixedFields'] : false;

        $actionNames = ['create', 'find', 'update', 'delete'];
        $constraints = [];
        $actions = [];

        foreach ($actionNames as $actionName) {

            if (!isset($config[$actionName])) {
                continue;
            }

            $configOfTheAction = $config[$actionName];

            if (isset($configOfTheAction['constraints'])) {
                $constraints[$actionName] = $configOfTheAction['constraints'];
                if (!is_array($configOfTheAction['constraints'])) {
                    throw new \RuntimeException('invalid constraints');
                }
                foreach ($constraints[$actionName] as $constraint) {
                    if (!($constraint instanceof Constraint)) {
                        throw new \RuntimeException('invalid constraint');
                    }
                }
            }

            if (isset($configOfTheAction['action'])) {
                $actions[$actionName] = $configOfTheAction['action'];
                if ($configOfTheAction['action'] && !is_a($configOfTheAction['action'], 'Core\Action\Action')) {
                    throw new \RuntimeException('invalid action');
                }
            }

        }

        $this->modelAspects[] =
            new ModelAspect($model, $prefix, $constraints, $actions, $relativeField, $withPrefixedFields);

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
                                    array $params = [], $hydrateArray = false, array $disabledKeyPaths = []) {


        $appCtx = ApplicationContext::getInstance();

        $registry = $appCtx->getNewRegistry();

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

        $result = $registry->find($qryCtx);


        if ($hydrateArray) {
            foreach ($result as &$data) {
                if (is_array($data)) {
                    foreach ($data as &$object) {
                        if (is_object($object)) {
                            $object = (new ModelConverter())->toArray($object, array_merge($fields,$returnedFields));
                        }
                    }
                }
                else {
                    $data = (new ModelConverter())->toArray($data, array_merge($fields,$returnedFields));
                }
            }
        }

        $result = $hydrateArray ? $this->formatFindResultArray($result) : $this->formatFindResultToObject($result);

        $this->enableModelAspects();


        return $result;

    }

    /**
     * @param Array $values
     *
     * @return Array
     */
    protected function formatFindValues ($values) {

        $formattedValues = [];

        foreach ($values as $value) {

            $explodedValue = explode('.', $value);
            $field = $explodedValue[count($explodedValue) - 1];
            $prefixed = false;

            if (count($explodedValue) > 1) {
                array_splice($explodedValue, count($explodedValue) - 1, 1);
                $value = implode('.', $explodedValue);
                foreach ($this->modelAspects as $modelAspect) {
                    if ($modelAspect->getPrefix() == $value) {
                        $value = $modelAspect->getRelativeField()->getValue() . '.' . $field;
                        $prefixed = true;
                        break;
                    }
                }
            }

            if (!$prefixed) {
                $value = $field;
            }

            $formattedValues[] = $value;

        }

        $formattedValues = $this->transformPrefixedFields($formattedValues);

        return $formattedValues;

    }

    /**
     * @param array $fields
     *
     * @return array
     */
    private function transformPrefixedFields (array $fields) {

        $newParams = [];
        $fields = is_array($fields) ? $fields : [$fields];

        foreach ($fields as $key) {

            if (strpos($key, '_') === false) {
                $newParams[] = $key;
                continue;
            }

            $field = $key;

            for ($i = 0; ; ++$i) {

                $explodedKey = explode('_', $field);
                $prefix = implode('_', array_slice($explodedKey, 0, $i + 1));
                $prefix = str_replace('_', '.', $prefix);

                if ($i + 1 == count($explodedKey) - 1) {

                    if (($relativeField = $this->isLinkedToModel($prefix))) {
                        $explodedRelativeField = explode('.', $relativeField);
                        $explodedRelativeField[] = $explodedKey[count($explodedKey) - 1];
                        $newParams[] = implode('.', $explodedRelativeField);
                    }

                    break;

                }

            }

        }

        return $newParams;

    }

    /**
     * @param array $result
     *
     * @return array
     */
    public function formatFindResultArray ($result) {

        $resultFormatted = [];

        if ($result) {
            foreach ($result as $elem) {
                $elem = $this->formatResultWithPrefixedFields($elem);
                $resultFormatted[] = $this->formatArrayWithPrefix($elem);
            }
        }

        return $resultFormatted;

    }

    /**
     * @param mixed $result
     *
     * @return array
     */
    public function formatResultWithPrefixedFields ($result) {

        $newResult = $result;

        foreach ($this->getModelAspectsWithPrefixedField() as $modelAspect) {

            $relativeField = $modelAspect->getRelativeField();

            if ($relativeField) {

                $explodedRelativeField = explode('.', $relativeField->getValue());

                $data = $newResult;
                foreach ($explodedRelativeField as $elem) {
                    if (!isset($data[$elem])) {
                        $data = [];
                        break;
                    }
                    $data = $data[$elem];
                }

                for ($i = count($explodedRelativeField) - 1; $i >= 0; --$i) {

                    $currentRelativeField = implode('.', array_slice($explodedRelativeField, 0, $i));

                    $currentModelAspect = $this->getModelAspectByRelativeField($currentRelativeField);

                    if (!$currentRelativeField
                        || ($currentModelAspect
                            && !$currentModelAspect->isWithPrefixedFields())
                    ) {

                        foreach ($data as $key => $value) {

                            $oneModelAspect =
                                $this->getModelAspectByRelativeField($relativeField->getValue() . '.' . $key);

                            if (!$oneModelAspect || !$oneModelAspect->isWithPrefixedFields()) {

                                $currentExplodedPrefix =
                                    $currentRelativeField ? explode('.', $currentModelAspect->getPrefix()) : [];
                                $explodedPrefix = explode('.', $modelAspect->getPrefix());
                                $explodedDiffPrefix = array_diff($explodedPrefix, $currentExplodedPrefix);

                                $prefixedKey = $key;
                                if ($oneModelAspect) {
                                    $explodedPrefix = explode('.', $oneModelAspect->getPrefix());
                                    $prefixedKey = $explodedPrefix[count($explodedPrefix) - 1];
                                }

                                $data[str_replace('.', '_', implode('.', $explodedDiffPrefix)) . '_' . $prefixedKey] =
                                    $value;

                            }

                            unset($data[$key]);

                        }

                        if ($currentRelativeField) {
                            $data = $this->buildArrayWithKeys(explode('.', $currentRelativeField), $data);
                        }

                        break;

                    }

                }

            }

            $newResult = ArrayExtra::array_merge_recursive_distinct($newResult, $data);

        }

        foreach ($this->getModelAspects() as $modelAspect) {

            $relativeField = $modelAspect->getRelativeField();

            if ($modelAspect->isWithPrefixedFields() && $relativeField) {
                $newResult = $this->removeKeysFromArray(explode('.', $relativeField->getValue()), $newResult);
            }

        }

        return $newResult;

    }

    /**
     * @return ModelAspect[]
     */
    private function getModelAspectsWithPrefixedField () {

        return array_filter($this->modelAspects, function ($modelAspect) {

            return $modelAspect->isEnabled() && $modelAspect->isWithPrefixedFields();

        });

    }

    /**
     * @param string $relativeField
     *
     * @return mixed
     */
    private function getModelAspectByRelativeField ($relativeField) {

        foreach ($this->modelAspects as $modelAspect) {

            if ($modelAspect->getRelativeField() && $modelAspect->getRelativeField()->getValue() == $relativeField) {
                return $modelAspect;
            }

        }

        return false;

    }

    /**
     * @param array $result
     *
     * @return array
     */
    private function formatArrayWithPrefix ($result) {

        $formattedResult = [];

        foreach ($this->getModelAspects() as $modelAspect) {

            $relativeField = $modelAspect->getRelativeField();
            $prefix = $modelAspect->getPrefix();

            if ($relativeField) {
                $explodedRelativeField = explode('.', $relativeField->getValue());
                $data = $result;
                foreach ($explodedRelativeField as $elem) {
                    if (!isset($data[$elem])) {
                        continue 2;
                    }
                    $data = $data[$elem];
                }
            }
            else {
                $data = $result;
            }

            if ($modelAspect->getPrefix()) {
                $explodedPrefix = explode('.', $modelAspect->getPrefix());
                $data = $this->buildArrayWithKeys($explodedPrefix, $data);
            }

            $formattedResult = ArrayExtra::array_merge_recursive_distinct($formattedResult, $data);

            if ($relativeField && $relativeField->getValue() != $prefix) {
                $formattedResult = $this->removeKeysFromArray($explodedRelativeField, $formattedResult);
            }

        }

        return $formattedResult;

    }

    /**
     * @param Array $result
     *
     * @return Array
     */
    protected function formatFindResultToObject ($result) {

        $entities = [];
        foreach ($result as $elem) {

            if (is_array($elem)) {
                $entities[] = $this->getMagicalEntityObject($elem[0]);
            }
            else {
                $entities[] = $this->getMagicalEntityObject($elem);
            }

        }

        return $entities;

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

}