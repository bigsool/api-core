<?php


namespace Archiweb\Validation;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;

abstract class ConstraintsProvider {

    /**
     * @param string $name
     * @param mixed  $value
     *
     * @return \Symfony\Component\Validator\ConstraintViolationListInterface
     */
    public function validate ($name, $value) {

        $constraints = $this->getConstraintsFor($name);

        return $constraints ? Validation::createValidator()->validate($value, $constraints)
            : new ConstraintViolationList();

    }

    /**
     * @param string $name
     *
     * @return Constraint|null
     */
    public function getConstraintsFor ($name) {

        $constraints = $this->listConstraints();

        return isset($constraints[$name]) ? $constraints[$name] : NULL;

    }

    /**
     * @return Constraint[]
     */
    protected abstract function listConstraints ();

} 