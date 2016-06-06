<?php

namespace Core\Module\Project;


use Core\Context\ApplicationContext;
use Core\Field\Calculated;
use Core\Field\CalculatedField;
use Core\Filter\Filter;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Boolean;
use Core\Validation\Parameter\DateTime;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\StringConstraint;

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

        $factory = ApplicationContext::getInstance()->getParameterFactory();

        return [
            'id'                   => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class,['max' => 32]),
                $factory->getParameter(NotBlank::class),
            ],
            'name'                 => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class,['max' => 255]),
                $factory->getParameter(NotBlank::class),
            ],
            'creationDate'         => [
                $factory->getParameter(DateTime::class),
                $factory->getParameter(NotBlank::class),
            ],
            'lastModificationDate' => [
                $factory->getParameter(DateTime::class),
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