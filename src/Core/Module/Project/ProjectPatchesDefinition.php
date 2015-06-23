<?php
/**
 * Created by PhpStorm.
 * User: bigsool
 * Date: 19/06/15
 * Time: 15:11
 */

namespace Core\Module\Project;


use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\DateTime;
use Core\Validation\Parameter\NotBlank;

class ProjectPatchesDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'ProjectPatches';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        return [
            'date' => [
                new DateTime(),
                new NotBlank(),
            ]
        ];

    }

}