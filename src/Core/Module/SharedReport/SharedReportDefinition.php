<?php


namespace Core\Module\SharedReport;


use Core\Context\ActionContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\DateTime;
use Core\Validation\Parameter\Int;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\String;

class SharedReportDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'SharedReport';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        return [
            'reportId'  => [new Int(), new NotBlank()],
            'hash'      => [new String(), new Length(['max' => 255]), new NotBlank()],
            'password'  => [new String(), new Length(['max' => 255])],
            'shareDate' => [new DateTime(), new NotBlank()]
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

        if (!$entityId) {
            $params['shareDate'] = new \DateTime();
            $params['hash'] = sha1(uniqid('', true));
        }

        $upsertContext = new ModuleEntityUpsertContext($this, $entityId, $params, $actionContext);

        return $upsertContext;

    }

}