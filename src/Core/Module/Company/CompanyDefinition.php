<?php


namespace Core\Module\Company;


use Core\Context\ApplicationContext;
use Core\Filter\Filter;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\StringConstraint;

class CompanyDefinition extends ModuleEntityDefinition {

    /**
     * @return string
     */
    public function getEntityName () {

        return 'Company';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        $factory = ApplicationContext::getInstance()->getParameterFactory();

        return [
            'name' =>
                [
                    $factory->getParameter(StringConstraint::class),
                    $factory->getParameter(Length::class, ['max' => 255]),
                    $factory->getParameter(NotBlank::class),
                ]
            ,
            'vat'  =>
                [
                    $factory->getParameter(StringConstraint::class),
                    $factory->getParameter(Length::class, ['max' => 255]),
                ]
            ,
        ];

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return [new StringFilter('Company', 'CompanyForId', 'id = :id')];

    }

}