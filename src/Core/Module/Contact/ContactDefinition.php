<?php


namespace Core\Module\Contact;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Email;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\StringConstraint;

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

        $factory = ApplicationContext::getInstance()->getParameterFactory();

        return [
            'label'    => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 255]),
                // new Nullable('') TODO
            ],
            'streets'  => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 65535]),
            ],
            'city'     => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 255]),
            ],
            'state'    => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 255]),
            ],
            'zip'      => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 255]),
            ],
            'country'  => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 255]),
            ],
            'mobile'   => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 255]),
            ],
            'landLine' => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 255]),
            ],
            'email'    => [
                $factory->getParameter(Email::class),
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