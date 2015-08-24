<?php


namespace Core\Module\Contact;


use Core\Context\ActionContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Email;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\String;

class ContactDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'Contact';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        return [
            'label'    => [
                new String(),
                new Length(['max' => 255]),
                // new Nullable('') TODO
            ],
            'streets'  => [
                new String(),
                new Length(['max' => 65535]),
            ],
            'city'     => [
                new String(),
                new Length(['max' => 255]),
            ],
            'state'    => [
                new String(),
                new Length(['max' => 255]),
            ],
            'zip'      => [
                new String(),
                new Length(['max' => 255]),
            ],
            'country'  => [
                new String(),
                new Length(['max' => 255]),
            ],
            'mobile'   => [
                new String(),
                new Length(['max' => 255]),
            ],
            'landLine' => [
                new String(),
                new Length(['max' => 255]),
            ],
            'email'    => [
                new Email(),
            ],
        ];

    }

    public function createUpsertContext (array $params, $entityId, ActionContext $actionContext) {

        $upsertContext = new ModuleEntityUpsertContext($this, $entityId, $params, $actionContext);

        // fix bug when update without any params, fix all fields to ''
        if (!$entityId) {
            // TODO : check with Thomas how ugly is it
            foreach (array_keys($this->getConstraintsList()) as $field) {
                $upsertContext->setDefaultParam($field, '');
            }
        }

        return $upsertContext;
    }

}