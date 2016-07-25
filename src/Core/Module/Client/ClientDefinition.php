<?php


namespace Core\Module\Client;


use Core\Context\ActionContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\StringConstraint;

class ClientDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'Client';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        return [
            'name'    => [
                new StringConstraint(),
                new Length(['max' => 255]),
                new NotBlank()
            ],
            'version' => [
                new StringConstraint(),
                new Length(['max' => 255]),
                new NotBlank()
            ],
            // TODO : LOCALE ?
        ];

    }

    /**
     * @param array         $params
     * @param int|null      $entityId
     * @param ActionContext $actionContext
     *
     * @return ModuleEntityUpsertContext
     */
    public function createUpsertContext (array $params, $entityId, ActionContext $actionContext) {

        $reqCtx = $actionContext->getRequestContext();

        if (!isset($params['name'])) {
            $params['name'] = $reqCtx->getClientName();
        }
        if (!isset($params['version'])) {
            $params['version'] = $reqCtx->getClientVersion();
        }
        // TODO : type

        $upsertContext = new ModuleEntityUpsertContext($this, $entityId, $params, $actionContext);

        return $upsertContext;

    }

    /**
     * @return \Core\Filter\Filter[]
     */
    public function getFilters () {

        return [
            new StringFilter('Client', 'ClientForDevice', 'device = :device'),
            new StringFilter('Client', 'ClientForName', 'name = :name'),
            new StringFilter('Client', 'ClientForVersion', 'version = :version'),
        ];

    }

}