<?php


namespace Core\Module;


use Core\Context\ActionContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Field\Calculated;
use Core\Filter\Filter;
use Core\Validation\ConstraintsProvider;
use Core\Validation\Parameter\Constraint;
use Core\Validation\Parameter\NotBlank;

abstract class ModuleEntityDefinition implements ConstraintsProvider {

    /**
     * @return Calculated[]
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
     * @param bool   $makeOptional
     *
     * @return \Core\Validation\Parameter\Constraint[]
     */
    public function getConstraintsFor (string $field, bool $makeOptional = FALSE) {

        $constraints = $this->getConstraintsList();

        return isset($constraints[$field])
            ? ($makeOptional
                ? array_filter($constraints[$field],
                    function (Constraint $c) {
                        return !($c instanceof NotBlank);
                    })
                : $constraints[$field])
            : [];

    }

    /**
     * @param                           $entity
     * @param ModuleEntityUpsertContext $context
     */
    public function postModify ($entity, ModuleEntityUpsertContext $context) {
    }

}