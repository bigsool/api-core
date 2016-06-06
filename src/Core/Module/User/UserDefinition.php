<?php


namespace Core\Module\User;


use Core\Context\ActionContext;
use Core\Context\ApplicationContext;
use Core\Context\ModuleEntityUpsertContext;
use Core\Filter\Filter;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Choice;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\NotNull;
use Core\Validation\Parameter\StringConstraint;

class UserDefinition extends ModuleEntityDefinition {

    /**
     * @param array         $params
     * @param int|null      $entityId
     * @param ActionContext $actionContext
     *
     * @return ModuleEntityUpsertContext
     */
    public function createUpsertContext (array $params, $entityId, ActionContext $actionContext) {

        if (!$entityId) {
            $params['creationDate'] = new \DateTime;

            // TODO : should be here ?
            if (!array_key_exists('lang', $params)) {
                $params['lang'] = $actionContext->getRequestContext()->getLocale();
            }

        }
        elseif (array_key_exists('creationDate', $params)) {
            unset($params['creationDate']);
        }

        // TODO : call parent or let as it
        $upsertContext = new ModuleEntityUpsertContext($this, $entityId, $params, $actionContext);

        return $upsertContext;

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        $factory = ApplicationContext::getInstance()->getParameterFactory();

        return [
            'firstName' =>
                [
                    $factory->getParameter(StringConstraint::class),
                    $factory->getParameter(Length::class, ['max' => 255]),
                    $factory->getParameter(NotNull::class),
                ]
            ,
            'lastName'  =>
                [
                    $factory->getParameter(StringConstraint::class),
                    $factory->getParameter(Length::class, ['max' => 255]),
                    $factory->getParameter(NotNull::class),
                ]
            ,
            'lang'      =>
                [
                    $factory->getParameter(Choice::class, ['choices' => ['fr', 'en']]),
                    $factory->getParameter(NotBlank::class),
                ]
            ,
        ];

    }

    /**
     * @return string
     */
    public function getEntityName () {

        return 'User';

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return [new StringFilter('User', 'UserForId', 'id = :id')];

    }

}