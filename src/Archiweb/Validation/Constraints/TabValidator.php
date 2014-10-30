<?php


namespace Archiweb\Validation\Constraints;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class TabValidator extends ConstraintValidator {

    /**
     * Checks if the passed value is valid.
     *
     * @param mixed      $value      The value that should be validated
     * @param Constraint $constraint The constraint for the validation
     *
     * @api
     */
    public function validate ($value, Constraint $constraint) {

        if (!is_array($value)) {
            $this->context->addViolation($constraint->message);
        }
    }
}