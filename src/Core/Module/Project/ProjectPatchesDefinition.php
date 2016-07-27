<?php

namespace Core\Module\Project;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\DateTime;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\StringConstraint;

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

        $factory = ApplicationContext::getInstance()->getParameterFactory();

        return [
            'id'   => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 32]),
                $factory->getParameter(NotBlank::class),
            ],
            'date' => [
                $factory->getParameter(DateTime::class),
                $factory->getParameter(NotBlank::class),
            ]
        ];

    }

    public function createUpsertContext (array $params, $entityId, ActionContext $actionContext) {

        $params['date'] = new \DateTime();

        return parent::createUpsertContext($params, $entityId, $actionContext);

    }

}