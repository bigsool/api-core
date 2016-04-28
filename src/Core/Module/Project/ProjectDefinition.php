<?php

namespace Core\Module\Project;


use Core\Field\Calculated;
use Core\Field\CalculatedField;
use Core\Filter\Filter;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Boolean;
use Core\Validation\Parameter\DateTime;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\String;

class ProjectDefinition extends ModuleEntityDefinition {

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
            'id'                   => [
                new String(),
                new Length(['max' => 32]),
                new NotBlank(),
            ],
            'name'                 => [
                new String(),
                new Length(['max' => 255]),
                new NotBlank(),
            ],
            'bucket'                 => [
                new String(),
                new Length(['max' => 64]),
                new NotBlank(),
            ],
            'region'                 => [
                new String(),
                new Length(['max' => 32]),
                new NotBlank(),
            ],
            'creationDate'         => [
                new DateTime(),
                new NotBlank(),
            ],
            'lastModificationDate' => [
                new DateTime(),
            ]
        ];

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return [
            new StringFilter('Project', 'ProjectForId', 'id = :id'),
        ];

    }

    /**
     * @return Calculated[]
     */
    public function getFields () {

        return [
            'isLocal' => new CalculatedField(function ($patches) {

                return !count($patches);

            }, ['patches.id'], true),
        ];

    }

}