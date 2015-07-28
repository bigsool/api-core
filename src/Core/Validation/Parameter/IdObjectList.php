<?php
/**
 * Created by PhpStorm.
 * User: bigsool
 * Date: 15/07/15
 * Time: 16:10
 */

namespace Core\Validation\Parameter;


use Core\Validation\Parameter\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class IdObjectList extends Callback {

    public function __construct () {

        parent::__construct(function ($list, ExecutionContextInterface $context) {

            if (!is_array($list)) {
                $context->addViolation('This value must be an array');

                return;
            }

            if (!array_key_exists('id', $list)) {
                $context->addViolation('This value must contain the key id');

                return;
            }

        });

    }
}