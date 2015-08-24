<?php


namespace Core\Context;


use Core\Error\Error;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;
use Core\Parameter\UnsafeParameter;
use Core\Util\ArrayExtra;
use Core\Validation\Parameter\Constraint;
use Core\Validation\Validator;

class ModuleEntityUpsertContext {

    /**
     * @var ActionContext
     */
    protected $actionContext;

    /**
     * @var int
     */
    protected $entityId;

    /**
     * @var mixed
     */
    protected $entity;

    /**
     * @var ModuleEntityDefinition
     */
    protected $definition;

    /**
     * @var Error[]
     */
    protected $errors = [];

    /**
     * @var Constraint[][]
     */
    protected $constraints;

    /**
     * @var array
     */
    protected $params;

    /**
     * @var array
     */
    protected $inputParams;

    /**
     * @param ModuleEntityDefinition $definition
     * @param int|null               $entityId
     * @param array                  $params
     * @param ActionContext          $actionContext
     */
    public function __construct (ModuleEntityDefinition $definition, $entityId, array $params,
                                 ActionContext $actionContext) {

        $this->actionContext = $actionContext;
        $this->entityId = $entityId;

        // TODO : be sure to test this line in the unit tests in different cases
        $this->params = $this->inputParams = $params;

        $this->definition = $definition;
        $this->constraints = $this->definition->getConstraintsList();
    }

    /**
     * @return mixed
     */
    public function getEntity () {

        if (!$this->entity && $this->getEntityId()) {

            $entityName = $this->getDefinition()->getEntityName();
            $dbEntityName = $this->getDefinition()->getDBEntityName();
            $reqCtx = $this->getActionContext()->getRequestContext()->copyWithoutRequestedFields();

            $qryCtx = new FindQueryContext($entityName, $reqCtx);
            $qryCtx->addField('*');
            $qryCtx->addFilter(new StringFilter($dbEntityName, '', 'id = :id'), $this->getEntityId());

            $this->entity = $qryCtx->findOne();

        }

        return $this->entity;

    }

    /**
     * @param mixed $entity
     */
    public function setEntity ($entity) {

        $this->entity = $entity;

    }

    /**
     * @return int
     */
    public function getEntityId () {

        return $this->entityId;

    }

    /**
     * @return ModuleEntityDefinition
     */
    public function getDefinition () {

        return $this->definition;

    }

    /**
     * @return ActionContext
     */
    public function getActionContext () {

        return $this->actionContext;

    }

    /**
     * @return bool
     */
    public function isUpdate () {

        return !$this->isCreation();

    }

    /**
     * @return bool
     */
    public function isCreation () {

        return $this->getEntityId() == NULL;

    }

    /**
     * @param array          $params
     * @param Constraint[][] $additionalConstraints
     */
    public function addParams (array $params, array $additionalConstraints = []) {

        $this->params = ArrayExtra::array_merge_recursive_distinct($this->params, $params);

        // TODO : this should be more complicated, doesn't work as is
        $this->constraints = array_merge($this->constraints, $additionalConstraints);

    }

    /**
     * @param string $field
     * @param mixed  $value
     */
    public function addParam ($field, $value) {

        ArrayExtra::magicalSet($this->params, $field, $value);

    }

    /**
     * @param string       $field
     *
     * @param Constraint[] $constraints
     *
     * @return mixed
     */
    public function getValidatedParam ($field, $constraints = NULL) {

        return $this->validateParam($constraints, $field, $this->params);
    }

    /**
     * @return array
     */
    public function getValidatedParams () {

        $this->validateParams();

        // TODO : throw anything ?

        return array_filter($this->params, function ($param) {

            return !($param instanceof UnsafeParameter);

        });

    }

    /**
     * @return array
     */
    public function getInputParams () {

        return $this->inputParams;

    }

    /**
     * @param string $key
     * @param mixed  $defaultValue
     *
     * @return mixed
     */
    public function getInputParam ($key, $defaultValue = NULL) {

        return isset($this->inputParams[$key]) ? $this->inputParams[$key] : $defaultValue;

    }

    public function getValidatedInputParam ($key) {

        return $this->validateParam(NULL, $key, $this->inputParams);

    }

    /**
     * @param bool $shouldThrowException
     *
     * @throws \Core\Error\ValidationException
     */
    public function validateParams ($shouldThrowException = false) {

        $validationResult = Validator::validateParams($this->constraints, $this->getParams(), $this->isUpdate());
        $validatedParams = $validationResult->getValidatedParams();
        $this->params = ArrayExtra::array_merge_recursive_distinct($this->params, $validatedParams);
        $this->addErrors($validationResult->getErrors());

        if ($shouldThrowException) {
            $validationResult->throwIfErrors();
        }

    }

    /**
     * Return safe and unsafe parameters
     * @return array
     */
    public function getParams () {

        return $this->params;

    }

    /**
     * @param $key
     * @param $defaultValue
     */
    public function setDefaultParam ($key, $defaultValue) {

        if (!array_key_exists($key, $this->params)) {
            $this->addParam($key, $defaultValue);
        }

    }

    /**
     * @param Error[] $errors
     */
    public function addErrors (array $errors) {

        $this->errors = array_merge($this->errors, $errors);

    }

    /**
     * @return Error[]
     */
    public function getErrors () {

        return $this->errors;

    }

    /**
     * @param Error[] $errors
     */
    public function setErrors (array $errors) {

        $this->errors = $errors;

    }

    /**
     * @param $constraints
     * @param $field
     *
     * @return mixed
     */
    protected function validateParam ($constraints, $field, array $params) {

        if (!$constraints) {
            // TODO  : what to do if no constraints found ?
            $constraints = isset($this->constraints[$field]) ? $this->constraints[$field] : [];
        }

        // TODO : what should I return if forceOptional = true and $field not defined in $params ?
        // Depending on this answer i should use Validator::validateParam
        $validationResult = Validator::validateParams([$field => $constraints], $params, $this->isUpdate());

        return $validationResult->getValidatedValue($field);
    }

}