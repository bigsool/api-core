<?php


namespace Core\Module\Contact;

use Core\Validation\AbstractConstraintsProvider;
use Core\Validation\Parameter\Email;

class Validation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'email' => [
                new Email()
            ]
        ];

    }
}