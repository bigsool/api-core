<?php


namespace Core\Module\Contact;

use Core\Validation\AbstractConstraintsProvider;
use Core\Validation\Parameter\Email;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\String;

class Validation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'label'    => [
                new String(),
                new Length(['max' => 255]),
            ],
            'streets'  => [
                new String(),
                new Length(['max' => 65535]),
            ],
            'city'     => [
                new String(),
                new Length(['max' => 255]),
            ],
            'state'    => [
                new String(),
                new Length(['max' => 255]),
            ],
            'zip'      => [
                new String(),
                new Length(['max' => 255]),
            ],
            'country'  => [
                new String(),
                new Length(['max' => 255]),
            ],
            'mobile'   => [
                new String(),
                new Length(['max' => 255]),
            ],
            'landLine' => [
                new String(),
                new Length(['max' => 255]),
            ],
            'email'    => [
                new Email(),
            ],
        ];

    }
}