<?php


namespace Core\Module\Credential;


use Core\Context\ApplicationContext;
use Core\Validation\ConstraintsProvider;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\Object;
use Doctrine\DBAL\Schema\Constraint;

class AuthenticationValidation implements ConstraintsProvider {

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

        $factory = ApplicationContext::getInstance()->getParameterFactory();

        return ['authToken' =>
                    [
                        $factory->getParameter(Object::class),
                        $factory->getParameter(NotBlank::class),
                    ]
        ];

    }

}