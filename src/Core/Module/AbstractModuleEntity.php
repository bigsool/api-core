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
     * @param ApplicationContext     $applicationContext
     * @param ModuleEntityDefinition $definition
     */
    public function __construct (ApplicationContext $applicationContext, ModuleEntityDefinition $definition) {

        $this->definition = $definition;
        $this->applicationContext = $applicationContext;

    }

    /**
     * @param array         $params
     * @param ActionContext $actionContext
     *
     * @return mixed
     * @throws \Core\Error\FormattedError
     */
    public function create (array $params, ActionContext $actionContext) {

        return $this->modifyEntity($params, NULL, $actionContext);

    }

    /**
     * @param int           $entityId
     * @param array         $params
     * @param ActionContext $actionContext
     *
     * @return mixed
     * @throws \Core\Error\FormattedError
     */
    public function update ($entityId, array $params, ActionContext $actionContext) {

        return $this->modifyEntity($params, $entityId, $actionContext);

    }

    /**
     * @param mixed $entity
     */
    public function delete ($entity) {

        $realModelClassName = Registry::realModelClassName($this->definition->getEntityName());
        $className = '\\' . get_class($entity);
        if (!($entity instanceof $realModelClassName)) {
            throw new \RuntimeException(sprintf('$entity must be a %s, %s %s given', $realModelClassName,
                                                gettype($entity), $className));
        }

        (new Registry)->delete($entity);

    }

    /**
     * @param FindQueryContext $findQueryContext
     *
     * @return array
     */
    public function find (FindQueryContext $findQueryContext) {

        return (new Registry)->find($findQueryContext);

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

        $realModelClassName = Registry::realModelClassName($this->definition->getEntityName());
        $className = '\\' . get_class($entity);
        if (!($entity instanceof $realModelClassName)) {
            throw new \RuntimeException(sprintf('$entity must be a %s, %s %s given', $realModelClassName,
                                                gettype($entity), $className));
        }

        (new Registry)->save($entity);

    }

    /**
     * @param array         $params
     * @param int|null      $entityId
     *
     * @param ActionContext $actionContext
     *
     * @return mixed
     * @throws \Core\Error\FormattedError
     */
    protected function modifyEntity (array $params, $entityId, ActionContext $actionContext) {

        try {
            $upsertContext = $this->createUpsertContextProxy($params, $entityId, $actionContext);
            // TODO : validate ?

            if ($upsertContext->getErrors()) {
                throw new ValidationException($upsertContext->getErrors());
            }

            $entity = $this->upsert($upsertContext);
            // TODO : save ?

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
     * @param array         $params
     * @param int|null      $entityId
     * @param ActionContext $actionContext
     *
     * @return ModuleEntityUpsertContext
     */
    protected function createUpsertContextProxy (array $params, $entityId, ActionContext $actionContext) {

        return $this->getDefinition()->createUpsertContext($params, $entityId, $actionContext);

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

        $className = Registry::realModelClassName($this->getDefinition()->getEntityName());

        return new $className;

    }

}