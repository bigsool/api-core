<?php


namespace Core\Validation;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;

abstract class AbstractConstraintsProvider implements ConstraintsProvider {

    /**
     * @param string $name
     *
     * @return Constraint[]|null
     */
    public function getConstraintsFor ($name) {

        $constraints = $this->listConstraints();

        return isset($constraints[$name]) ? $constraints[$name] : NULL;

    }

    /**
     * @param string $name
     * @param mixed  $value
     * @param bool   $forceOptional
     *
     * @return ConstraintViolationList|\Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validate ($name, $value, $forceOptional = false) {

        $constraints = $this->getConstraintsFor($name);
        if ($forceOptional) {
            $constraints = array_reduce($constraints, function ($constraints, Constraint $constraint) {

                if (!($constraint instanceof Assert\NotBlank)) {
                    $constraints[] = $constraint;
                }

                return $constraints;

            }, []);
        }

        return $constraints ? Validation::createValidator()->validate($value, $constraints)
            : new ConstraintViolationList();

    }

    /**
     * @return Constraint[][]
     */
    protected abstract function listConstraints ();

} 