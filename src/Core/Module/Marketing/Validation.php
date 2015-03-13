<?php


namespace Core\Module\Marketing;


use Core\Validation\AbstractConstraintsProvider;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\String;

class Validation extends AbstractConstraintsProvider {

    /**
     * @return Constraint[][]
     */
    protected function listConstraints () {

        return [
            'knowsFrom' => [
                new String(),
                new Length(['max' => 255]),
            ],
        ];

    }
}