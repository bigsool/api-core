<?php


namespace Core\Context;


use Core\Error\Error;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;

class ModuleEntityUpsertContext {

    /**
     * @var ActionContext
     */
    protected $actionContext;

    /**
     * @var array
     */
    protected $inputParams;

    /**
     * @var array
     */
    protected $validatedParams;

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
     * @param ModuleEntityDefinition $definition
     * @param int|null               $entityId
     * @param ActionContext          $actionContext
     */
    public function __construct (ModuleEntityDefinition $definition, $entityId = NULL, ActionContext $actionContext) {

        $this->actionContext = $actionContext;
        $this->entityId = $entityId;
        $this->inputParams = $actionContext->getParams();
        $this->validatedParams = $actionContext->getVerifiedParams();

        $this->definition = $definition;
    }

    /**
     * @return array
     */
    public function getInputParams () {

        return $this->inputParams;

    }

    /**
     * @return array
     */
    public function getValidatedParams () {

        return $this->validatedParams;

    }

    /**
     * @param array $validatedParams
     */
    public function setValidatedParams (array $validatedParams) {

        $this->validatedParams = $validatedParams;

    }

    /**
     * @return bool
     */
    public function isCreation () {

        return $this->getEntityId() == NULL;

    }

    /**
     * @return int
     */
    public function getEntityId () {

        return $this->entityId;

    }

    /**
     * @return mixed
     */
    public function getEntity () {

        if (!$this->entity) {

            if (!$this->getEntityId()) {
                throw new \RuntimeException('cannot find entity if id is not specified');
            }

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
     * @return mixed
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
     * @param Error[] $errors
     */
    public function addErrors(array $errors) {

        $this->errors = array_merge($this->errors, $errors);

    }

}