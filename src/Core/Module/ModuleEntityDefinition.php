<?php


namespace Core\Module;


use Core\Context\ActionContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Field\CalculatedField;
use Core\Filter\Filter;
use Core\Validation\ConstraintsProvider;

abstract class ModuleEntityDefinition implements ConstraintsProvider {

    /**
     * @return CalculatedField[]
     */
    public function getFields () {

        return [];

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return [];

    }

    /**
     * @return string
     */
    public function getDBEntityName () {

        return $this->getEntityName();

    }

    /**
     * @return string
     */
    public abstract function getEntityName ();

    /**
     * @param array         $params
     * @param int|null      $entityId
     * @param ActionContext $actionContext
     *
     * @return ModuleEntityUpsertContext
     */
    public function createUpsertContext (array $params, $entityId, ActionContext $actionContext) {

        $upsertContext = new ModuleEntityUpsertContext($this, $entityId, $params, $actionContext);

        return $upsertContext;

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public abstract function getConstraintsList ();

    /**
     * @param string $field
     *
     * @return \Core\Validation\Parameter\Constraint[]
     */
    public function getConstraintsFor ($field) {

        $constraints = $this->getConstraintsList();

        return isset($constraints[$field]) ? $constraints[$field] : [];

    }

    /**
     * @param                           $entity
     * @param ModuleEntityUpsertContext $context
     */
    public function postModify ($entity, ModuleEntityUpsertContext $context) {
    }

}