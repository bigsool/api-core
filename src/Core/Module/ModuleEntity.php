<?php


namespace Core\Module;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;

interface ModuleEntity {

    /**
     * @return ApplicationContext
     */
    public function getApplicationContext ();

    /**
     * @param FindQueryContext $findQueryContext
     *
     * @return array
     */
    public function find (FindQueryContext $findQueryContext);

    /**
     * @param array         $params
     * @param ActionContext $context
     *
     * @return mixed
     */
    public function create (array $params, ActionContext $context);

    /**
     * @param int           $entityId
     * @param array         $params
     * @param ActionContext $context
     *
     * @return mixed
     */
    public function update ($entityId, array $params, ActionContext $context);

    /**
     * @return ModuleEntityDefinition
     */
    public function getDefinition ();

    /**
     * @param mixed $entity
     */
    public function save ($entity);

    /**
     * @param mixed $entity
     */
    public function delete ($entity);

}