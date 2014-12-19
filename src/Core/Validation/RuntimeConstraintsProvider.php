<?php


namespace Core\Validation;


use Symfony\Component\Validator\Constraint;

class RuntimeConstraintsProvider extends AbstractConstraintsProvider {

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
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return $this->constraints;

    }
}