<?php


namespace Core\Module;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Registry;

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
     * @param ActionContext $context
     *
     * @return mixed
     */
    public function create (ActionContext $context);

    /**
     * @param ActionContext $context
     *
     * @return mixed
     */
    public function update (ActionContext $context);

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

    /**
     * @param Registry $registry
     */
    public function setRegistry (Registry $registry);

}