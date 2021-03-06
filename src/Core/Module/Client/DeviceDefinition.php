<?php


namespace Core\Module\Client;


use Core\Context\ApplicationContext;
use Core\Filter\Filter;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Choice;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\StringConstraint;

class DeviceDefinition extends ModuleEntityDefinition {

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        $factory = ApplicationContext::getInstance()->getParameterFactory();

        return [
            'UUID' => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 255])
            ],
            'name' => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 255]),
            ],
            'type' => [
                $factory->getParameter(Choice::class, ['choices' => ['ipad']]),
                $factory->getParameter(NotBlank::class),
            ],
        ];

    }

    /**
     * @return string
     */
    public function getEntityName () {

        return 'Device';

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return [new StringFilter('Device', 'DeviceForUUID', 'UUID = :UUID')];

    }

}