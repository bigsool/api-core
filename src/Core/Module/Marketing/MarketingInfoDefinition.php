<?php


namespace Core\Module\Marketing;


use Core\Context\ApplicationContext;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\StringConstraint;

class MarketingInfoDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'MarketingInfo';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        $factory = ApplicationContext::getInstance()->getParameterFactory();

        return [
            'knowsFrom' => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 255]),
            ],
        ];

    }

}