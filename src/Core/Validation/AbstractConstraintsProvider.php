<?php


namespace Core\Validation;

use Core\Context\ApplicationContext;
use Core\Parameter\UnsafeParameter;
use Core\Validation\Parameter\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Validation;

abstract class AbstractConstraintsProvider {

    /**
     * @param string $name
     * @param mixed  $value
     * @param        $path
     * @param bool   $forceOptional
     *
     * @return bool
     */
    public function validate ($name, $value, $path, $forceOptional = false) {

        $constraints = $this->getConstraintsFor($name);
        if ($forceOptional && $constraints) {
            $constraints = array_reduce($constraints, function ($constraints, Constraint $constraint) {

                if (!($constraint->getConstraint() instanceof Assert\NotBlank)
                    && !($constraint->getConstraint() instanceof Assert\NotNull)
                ) {
                    $constraints[] = $constraint;
                }

                return $constraints;

            }, []);
        }

        $isValid = true;

        if ($constraints) {
            foreach ($constraints as $constraint) {
                if (!($constraint instanceof Constraint)) {
                    throw new \RuntimeException('wrong constraint type');
                }
                $validator = Validation::createValidator();
                $violations = $validator->validate($value, [$constraint->getConstraint()]);
                if ($violations->count()) {
                    ApplicationContext::getInstance()->getErrorManager()->addError($constraint->getErrorCode(), $path);
                    $isValid = false;
                }
            }

        }

        return $isValid;

    }

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
     * @return Constraint[][]
     */
    protected abstract function listConstraints ();

} 