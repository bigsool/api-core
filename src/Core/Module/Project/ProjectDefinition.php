<?php
/**
 * Created by PhpStorm.
 * User: bigsool
 * Date: 19/06/15
 * Time: 15:09
 */

namespace Core\Module\Project;


use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\DateTime;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\String;

class ProjectDefinition extends ModuleEntityDefinition{

    /**
     * @return string
     */
    public function getEntityName () {

        return 'Project';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        return [
            'name' => [
                new String(),
                new Length(['max' => 255]),
                new NotBlank(),
            ],
            'creation_date' => [
                new DateTime(),
                new NotBlank(),
            ],
            'last_modification' => [
                new DateTime(),
            ],
        ];

    }

}