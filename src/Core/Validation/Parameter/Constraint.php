<?php


namespace Core\Validation\Parameter;


use Core\Context\ApplicationContext;
use Core\Error\Error;
use Core\Error\ErrorManager;
use Symfony\Component\Validator\Constraint as SfConstraint;

abstract class Constraint {

    /**
     * @var SfConstraint
     */
    protected $constraint;

    /**
     * @var int
     */
    protected $errorCode;

    /**
     * @return SfConstraint
     */
    public function getConstraint () {

        return $this->constraint;
    }

    /**
     * @param SfConstraint $constraint
     */
    protected function setConstraint (SfConstraint $constraint) {

        $this->constraint = $constraint;
    }

    /**
     * @return int
     */
    public function getErrorCode () {

        return $this->errorCode;
    }

    /**
     * @param int $errorCode
     */
    protected function setErrorCode ($errorCode) {

        $this->errorCode = $errorCode;
    }

}