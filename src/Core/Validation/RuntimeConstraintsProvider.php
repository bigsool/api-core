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

        foreach ($constraints as $constraintArray) {
            if (!is_array($constraintArray)) {
                throw new \RuntimeException('invalid constraint type');
            }
            foreach ($constraintArray as $field => $constraint) {
                if (!is_string($field) || !($constraint instanceof Constraint)) {
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