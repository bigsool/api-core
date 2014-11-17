<?php


namespace Core\Operator;


class OrOperator implements LogicOperator {

    /**
     * @return string
     */
    public function toDQL () {

        return 'OR';

    }

}