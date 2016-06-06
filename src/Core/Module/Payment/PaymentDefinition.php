<?php
/**
 * Created by PhpStorm.
 * User: bigsool
 * Date: 17/07/15
 * Time: 10:16
 */

namespace Core\Module\Payment;

use Core\Context\ApplicationContext;
use Core\Filter\Filter;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Choice;
use Core\Validation\Parameter\DateTime;
use Core\Validation\Parameter\FloatConstraint;
use Core\Validation\Parameter\Integer;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\StringConstraint;
use Core\Validation\Parameter\Text;


class PaymentDefinition extends ModuleEntityDefinition {

    const PAYMENT_STATUS_PAID = "PAID";

    const PAYMENT_STATUS_CANCELLED = "CANCELED";

    const PAYMENT_STATUS_PENDING = "PENDING";

    /**
     * @return string
     */
    public function getEntityName () {

        return 'Payment';

    }

    /**
     * @return \Core\Validation\Parameter\Constraint[][]
     */
    public function getConstraintsList () {

        $factory = ApplicationContext::getInstance()->getParameterFactory();

        return [
            'id'          => [
                $factory->getParameter(Integer::class),
                $factory->getParameter(Length::class, ['max' => 11]),
                $factory->getParameter(NotBlank::class),
            ],
            'gateway'     => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 255]),
                $factory->getParameter(NotBlank::class),
            ],
            'gatewayData' => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(NotBlank::class),
                $factory->getParameter(Length::class, ['max' => 65000]),
            ],
            'externalId'  => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 255]),
            ],
            'status'      => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 255]),
                $factory->getParameter(NotBlank::class),
                $factory->getParameter(Choice::class,
                    [
                        'choices' => [
                            static::PAYMENT_STATUS_PAID,
                            static::PAYMENT_STATUS_CANCELLED,
                            static::PAYMENT_STATUS_PENDING
                        ]
                    ]),
            ],
            'amount'      => [
                $factory->getParameter(FloatConstraint::class),
                $factory->getParameter(NotBlank::class),
            ],
            'vat'         => [
                $factory->getParameter(FloatConstraint::class),
                $factory->getParameter(NotBlank::class),
            ],
            'currency'    => [
                $factory->getParameter(StringConstraint::class),
                $factory->getParameter(Length::class, ['max' => 3]),
                $factory->getParameter(NotBlank::class),
            ],
            'date'        => [
                $factory->getParameter(DateTime::class),
                $factory->getParameter(NotBlank::class),
            ]
        ];

    }

    /**
     * @return Filter[]
     */
    public function getFilters () {

        return [
            new StringFilter('Payment', 'PaymentForGateway', 'gateway = :gateway'),
            new StringFilter('Payment', 'PaymentForExternalId', 'externalId = :externalId'),
        ];

    }

}