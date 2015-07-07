<?php
/**
 * Created by PhpStorm.
 * User: bigsool
 * Date: 19/06/15
 * Time: 15:09
 */

namespace Core\Module\Project;


use Core\Filter\Filter;
use Core\Filter\StringFilter;
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
            'id' => [
                new String(),
                new Length(['max' => 16]),
                new NotBlank(),
            ],
            'name' => [
                new String(),
                new Length(['max' => 255]),
                new NotBlank(),
            ],
            'creationDate' => [
                new DateTime(),
                new NotBlank(),
            ],
            'lastModification' => [
                new DateTime(),
            ],
        ];

    }


    /**
     * @return Filter[]
     */
    public function getFilters () {

        return [
          new StringFilter('Project','ProjectForId', 'id = :id'),
        ];

    }

}