<?php


namespace Core\Module;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Error\ValidationException;
use Core\Registry;

abstract class AbstractModuleEntity implements ModuleEntity {

    /**
     * @var ModuleEntityDefinition
     */
    protected $definition;

    /**
     * @var ApplicationContext
     */
    protected $applicationContext;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @param ApplicationContext     $applicationContext
     * @param ModuleEntityDefinition $definition
     */
    public function __construct (ApplicationContext $applicationContext, ModuleEntityDefinition $definition) {

        $this->definition = $definition;
        $this->applicationContext = $applicationContext;

    }

    /**
     * @param ActionContext $actionContext
     *
     * @return mixed
     */
    public function create (ActionContext $actionContext) {

        return $this->modifyEntity($actionContext->getParams(), NULL, $actionContext);

    }

    /**
     * @param mixed $entity
     */
    public function delete ($entity) {

        $realModelClassName = $this->registry->realModelClassName($this->definition);
        $className = '\\' . get_class($entity);
        if (!($entity instanceof $realModelClassName)) {
            throw new \RuntimeException(sprintf('$entity must be a %s, %s %s given', $realModelClassName,
                                                gettype($entity), $className));
        }

        $this->registry->delete($entity);

    }

    /**
     * @param FindQueryContext $findQueryContext
     *
     * @return array
     */
    public function find (FindQueryContext $findQueryContext) {

        return $this->registry->find($findQueryContext);

    }

    /**
     * @return ApplicationContext
     */
    public function getApplicationContext () {

        return $this->applicationContext;

    }

    /**
     * @return ModuleEntityDefinition
     */
    public function getDefinition () {

        return $this->definition;

    }

    /**
     * @param mixed $entity
     */
    public function save ($entity) {

        $realModelClassName = $this->registry->realModelClassName($this->definition);
        $className = '\\' . get_class($entity);
        if (!($entity instanceof $realModelClassName)) {
            throw new \RuntimeException(sprintf('$entity must be a %s, %s %s given', $realModelClassName,
                                                gettype($entity), $className));
        }

        $this->registry->save($entity);

    }

    /**
     * @param Registry $registry
     */
    public function setRegistry (Registry $registry) {

        $this->registry = $registry;

    }

    /**
     * @param ActionContext $actionContext
     *
     * @return mixed
     */
    public function update (ActionContext $actionContext) {

        // TODO : check me : how to get validated id ?
        $entityId = $actionContext->getVerifiedParam('id');

        return $this->modifyEntity($actionContext->getParams(), $entityId, $actionContext);

    }

    /**
     * @param array         $unsafeParams
     * @param int|null      $entityId
     *
     * @param ActionContext $actionContext
     *
     * @return mixed
     * @throws \Core\Error\FormattedError
     */
    protected function modifyEntity (array $unsafeParams, $entityId, ActionContext $actionContext) {

        try {
            $upsertContext = $this->createUpsertContextProxy($unsafeParams, $entityId, $actionContext);
            if ($upsertContext->getErrors()) {
                throw new ValidationException($upsertContext->getErrors());
            }

            $entity = $this->upsert($upsertContext);

            $this->postModifyProxy($entity, $upsertContext);

            return $entity;
        }
        catch (ValidationException $exception) {
            $errMgr = $actionContext->getApplicationContext()->getErrorManager();
            $errMgr->addErrors($exception->getErrors());

            throw $errMgr->getFormattedError();
        }

    }

    /**
     * @param array         $unsafeParams
     * @param int|null      $entityId
     * @param ActionContext $actionContext
     *
     * @return ModuleEntityUpsertContext
     */
    protected function createUpsertContextProxy (array $unsafeParams, $entityId, ActionContext $actionContext) {

        return $this->getDefinition()->createUpsertContext($unsafeParams, $entityId, $actionContext);

    }

    /**
     * @param ModuleEntityUpsertContext $upsertContext
     *
     * @return mixed
     */
    abstract protected function upsert (ModuleEntityUpsertContext $upsertContext);

    /**
     * @param mixed                     $entity
     * @param ModuleEntityUpsertContext $upsertContext
     */
    protected function postModifyProxy ($entity, ModuleEntityUpsertContext $upsertContext) {

        $this->getDefinition()->postModify($entity, $upsertContext);

    }

    /**
     * @return mixed
     */
    protected function createEntity () {

        $className = $this->registry->realModelClassName($this->getDefinition()->getEntityName());

        return new $className;

    }

}