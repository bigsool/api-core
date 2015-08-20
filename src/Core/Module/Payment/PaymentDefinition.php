<?php
/**
 * Created by PhpStorm.
 * User: bigsool
 * Date: 17/07/15
 * Time: 10:16
 */

namespace Core\Module\Payment;

use Core\Filter\Filter;
use Core\Filter\StringFilter;
use Core\Module\ModuleEntityDefinition;
use Core\Validation\Parameter\Choice;
use Core\Validation\Parameter\DateTime;
use Core\Validation\Parameter\Float;
use Core\Validation\Parameter\Int;
use Core\Validation\Parameter\Length;
use Core\Validation\Parameter\NotBlank;
use Core\Validation\Parameter\String;
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

        return [
            'id'          => [
                new Int(),
                new Length(['max' => 11]),
                new NotBlank(),
            ],
            'gateway'     => [
                new String(),
                new Length(['max' => 255]),
                new NotBlank(),
            ],
            'gatewayData' => [
                new String(),
                new NotBlank(),
                new Length(['max' => 65000]),
            ],
            'externalId'  => [
                new String(),
                new Length(['max' => 255]),
            ],
            'status'      => [
                new String(),
                new Length(['max' => 255]),
                new NotBlank(),
                new Choice(
                    [
                        'choices' => [
                            static::PAYMENT_STATUS_PAID,
                            static::PAYMENT_STATUS_CANCELLED,
                            static::PAYMENT_STATUS_PENDING
                        ]
                    ]),
            ],
            'amount'      => [
                new Float(),
                new NotBlank(),
            ],
            'vat'         => [
                new Float(),
                new NotBlank(),
            ],
            'currency'    => [
                new String(),
                new Length(['max' => 3]),
                new NotBlank(),
            ],
            'date'        => [
                new DateTime(),
                new NotBlank(),
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