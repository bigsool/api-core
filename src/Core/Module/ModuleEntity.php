<?php


namespace Core\Module;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\FindQueryContext;
use Core\Field\CalculatedField;
use Core\Filter\Filter;
use Core\Registry;

interface ModuleEntity {

    /**
     * @param ApplicationContext $applicationContext
     * @param string             $entityName
     * @param CalculatedField[]  $fields
     * @param Filter[]           $filters
     */
    public function __construct (ApplicationContext $applicationContext, $entityName, array $fields = [],
                                 array $filters = []);

    /**
     * @return callable[]
     */
    public function getCalculatedFieldCallbacks ();

    /**
     * @return Filter[]
     */
    public function getFilters ();

    /**
     * @param FindQueryContext $findQueryContext
     *
     * @return array
     */
    public function find (FindQueryContext $findQueryContext);

    /**
     * @param ActionContext $context
     * @param array         $params
     *
     * @return mixed
     */
    public function create (ActionContext $context, array $params);

    /**
     * @return string
     */
    public function getEntityName ();

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
    public function setRegistry(Registry $registry);

}