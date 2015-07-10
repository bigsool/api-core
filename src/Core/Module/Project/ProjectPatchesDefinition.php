<?php
/**
 * Created by PhpStorm.
 * User: bigsool
 * Date: 19/06/15
 * Time: 15:11
 */

namespace Core\Module\Project;


use Core\Context\ActionContext;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\DateTime;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\String;

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
            'id' => [
                new String(),
                new Length(['max' => 32]),
                new NotBlank(),
            ],
            'date' => [
                new DateTime(),
                new NotBlank(),
            ]
        ];

    }

    public function createUpsertContext (array $params, $entityId, ActionContext $actionContext) {

        $params['date'] = new \DateTime();

        return parent::createUpsertContext($params, $entityId, $actionContext);

    }

}