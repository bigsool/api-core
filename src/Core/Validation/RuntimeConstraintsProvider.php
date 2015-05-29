<?php


namespace Core\Validation;


use Core\Validation\Parameter\Constraint;

class RuntimeConstraintsProvider implements ConstraintsProvider {

    /**
     * @var Constraint[][]
     */
    protected $constraints;

    /**
     * @param Constraint[][] $constraints
     */
    public function __construct (array $constraints = []) {

        foreach ($constraints as $field => $constraintArray) {
            if (!is_string($field) || !is_array($constraintArray)) {
                throw new \RuntimeException('invalid constraint type');
            }
            foreach ($constraintArray as $constraint) {
                if (!($constraint instanceof Constraint)) {
                    throw new \RuntimeException('invalid constraint type');
                }
            }
        }

        $this->constraints = $constraints;

    }

    /**
     * @param string $field
     * @param array  $params
     *
     * @return Constraint[]|null
     */
    public function getConstraintsFor ($field, array &$params) {

        $constraints = $this->getConstraintsList($params);

        return isset($constraints[$field]) ? $constraints[$field] : NULL;

    }

    /**
     * @param array $params
     *
     * @return Constraint[][]
     */
    public function getConstraintsList (array &$params) {

        return $this->constraints;

    }
}