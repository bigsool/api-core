<?php


namespace Core\Module;


use Core\Context\ActionContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Field\CalculatedField;
use Core\Filter\Filter;
use Core\Validation\ConstraintsProvider;
use Core\Validation\RuntimeConstraintsProvider;
use Core\Validation\Validator;

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
     * @param array         $unsafeParams
     * @param int|null      $entityId
     * @param ActionContext $actionContext
     *
     * @return ModuleEntityUpsertContext
     */
    public function createUpsertContext (array $unsafeParams, $entityId, ActionContext $actionContext) {

        $upsertContext = new ModuleEntityUpsertContext($this, $entityId, $actionContext);

        Validator::validateFields($this->getConstraints(), $unsafeParams, $upsertContext->isCreation());

        return $upsertContext;

    }

    /**
     * @param                           $entity
     * @param ModuleEntityUpsertContext $context
     */
    public function postModify ($entity, ModuleEntityUpsertContext $context) {
    }

    /**
     * @param bool          $isCreation
     * @param ActionContext $actionContext
     * @param bool          $forceOptional
     *
     * @throws \Core\Error\FormattedError
     */
    protected function validate ($isCreation, ActionContext $actionContext, $forceOptional = NULL) {

        if (is_null($forceOptional)) {
            $forceOptional = !$isCreation;
        }

        //$this->preValidate($isCreation, $actionContext, $forceOptional);

        $formattedConstraints = [];
        foreach ($this->getConstraints() as $field => $constraints) {
            $formattedConstraints[$field] = [new RuntimeConstraintsProvider([$field => $constraints]), $forceOptional];
        }


        Validator::validate($actionContext, $formattedConstraints);

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public abstract function getConstraints ();

    /**
     * @param string $field
     *
     * @return \Core\Validation\Parameter\Constraint[]|null
     */
    public function getConstraintsFor ($field) {

        $constraints = $this->getConstraints();

        return isset($constraints[$field]) ? $constraints[$field] : NULL;

    }

}