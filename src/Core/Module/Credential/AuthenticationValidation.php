<?php


namespace Core\Module\Credential;


use Core\Validation\ConstraintsProvider;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\Object;
use Doctrine\DBAL\Schema\Constraint;

class AuthenticationValidation implements ConstraintsProvider {

    /**
     * @param string $field
     *
     * @return Constraint[]|null
     */
    public function getConstraintsFor ($field) {

        $constraints = $this->getConstraintsList();

        return isset($constraints[$field]) ? $constraints[$field] : NULL;

    }

    /**
     * @return Constraint[][]
     */
    public function getConstraintsList () {

        return ['authToken' =>
                    [
                        new Object(),
                        new NotBlank(),
                    ]
        ];

    }

}