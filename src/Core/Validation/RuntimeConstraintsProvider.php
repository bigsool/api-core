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
     *
     * @param bool   $makeOptional
     * @return Constraint[]
     */
    public function getConstraintsFor (string $field, bool $makeOptional = FALSE) {

        $constraints = $this->getConstraintsList();

        return isset($constraints[$field]) ? $constraints[$field] : [];

    }

    /**
     * @return Constraint[][]
     */
    public function getConstraintsList () {

        return $this->constraints;

    }
}